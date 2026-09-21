<?php
/**
 * Prospect Digital — Admin Helper Functions
 * ---------------------------------------------------------------------------
 * Isolated helper library for the administrative dashboard.
 * Does not interfere with front-end public functions.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

if (!defined('ADMIN_PANEL_ACTIVE')) {
    define('ADMIN_PANEL_ACTIVE', true);
}

// Ensure base configuration is loaded
require_once dirname(__DIR__, 2) . '/includes/config.php';

define('ADMIN_DATA_DIR', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'admin');
define('ADMIN_CONFIG_FILE', ADMIN_DATA_DIR . DIRECTORY_SEPARATOR . 'config.php');
define('ADMIN_META_FILE', ADMIN_DATA_DIR . DIRECTORY_SEPARATOR . 'enquiry_meta.json');
define('ADMIN_RATE_FILE', ADMIN_DATA_DIR . DIRECTORY_SEPARATOR . 'login_rate.json');

/** Load admin configuration */
function admin_config(): array
{
    if (!is_file(ADMIN_CONFIG_FILE)) {
        return ['users' => [], 'settings' => []];
    }
    return require ADMIN_CONFIG_FILE;
}

/** Save admin configuration atomically */
function admin_save_config(array $config): bool
{
    $export = var_export($config, true);
    $content = "<?php\ndeclare(strict_types=1);\nif (!defined('ADMIN_PANEL_ACTIVE')) { http_response_code(403); exit('Direct access not permitted.'); }\nreturn " . $export . ";\n";
    return (bool) @file_put_contents(ADMIN_CONFIG_FILE, $content, LOCK_EX);
}

/** Generate or retrieve Admin CSRF Token */
function admin_csrf_token(): string
{
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

/** Verify CSRF Token */
function admin_verify_csrf(?string $token): bool
{
    if (empty($token) || empty($_SESSION['admin_csrf'])) {
        return false;
    }
    return hash_equals($_SESSION['admin_csrf'], $token);
}

/** Check rate limiting for login attempts */
function admin_check_rate_limit(string $ip): array
{
    $max_attempts = 5;
    $lockout_seconds = 900; // 15 minutes

    if (!is_file(ADMIN_RATE_FILE)) {
        return ['allowed' => true, 'remaining_seconds' => 0];
    }

    $data = @json_decode((string) @file_get_contents(ADMIN_RATE_FILE), true) ?: [];
    $ip_hash = hash('sha256', $ip . '|pd_admin');

    if (!isset($data[$ip_hash])) {
        return ['allowed' => true, 'remaining_seconds' => 0];
    }

    $record = $data[$ip_hash];
    $attempts = (int) ($record['attempts'] ?? 0);
    $last_attempt = (int) ($record['last_attempt'] ?? 0);
    $elapsed = time() - $last_attempt;

    if ($attempts >= $max_attempts) {
        if ($elapsed < $lockout_seconds) {
            return [
                'allowed' => false,
                'remaining_seconds' => $lockout_seconds - $elapsed,
            ];
        }
        // Lockout expired, reset
        unset($data[$ip_hash]);
        @file_put_contents(ADMIN_RATE_FILE, json_encode($data), LOCK_EX);
    }

    return ['allowed' => true, 'remaining_seconds' => 0];
}

/** Record a failed login attempt */
function admin_record_failed_login(string $ip): void
{
    $data = is_file(ADMIN_RATE_FILE) ? (@json_decode((string) @file_get_contents(ADMIN_RATE_FILE), true) ?: []) : [];
    $ip_hash = hash('sha256', $ip . '|pd_admin');

    $attempts = (int) ($data[$ip_hash]['attempts'] ?? 0) + 1;
    $data[$ip_hash] = [
        'attempts'     => $attempts,
        'last_attempt' => time(),
    ];

    @file_put_contents(ADMIN_RATE_FILE, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

/** Reset failed login attempts on successful login */
function admin_reset_failed_logins(string $ip): void
{
    if (!is_file(ADMIN_RATE_FILE)) {
        return;
    }
    $data = @json_decode((string) @file_get_contents(ADMIN_RATE_FILE), true) ?: [];
    $ip_hash = hash('sha256', $ip . '|pd_admin');

    if (isset($data[$ip_hash])) {
        unset($data[$ip_hash]);
        @file_put_contents(ADMIN_RATE_FILE, json_encode($data), LOCK_EX);
    }
}

/** Authenticate an administrator */
function admin_authenticate(string $username, string $password): ?array
{
    $config = admin_config();
    $users = $config['users'] ?? [];

    $username = trim($username);
    if (!isset($users[$username])) {
        return null;
    }

    $user = $users[$username];
    if (password_verify($password, $user['password_hash'])) {
        // Update last login
        $config['users'][$username]['last_login'] = date('c');
        admin_save_config($config);
        return $user;
    }

    return null;
}

/** Read enquiry metadata index */
function admin_get_enquiry_metadata(): array
{
    if (!is_file(ADMIN_META_FILE)) {
        return [];
    }
    return @json_decode((string) @file_get_contents(ADMIN_META_FILE), true) ?: [];
}

/** Update enquiry metadata */
function admin_update_enquiry_meta(string $ref, array $updates): bool
{
    $meta = admin_get_enquiry_metadata();
    $existing = $meta[$ref] ?? [
        'status'         => 'New',
        'notes'          => '',
        'priority'       => 'Normal',
        'status_history' => [],
    ];

    $previous_status = $existing['status'] ?? 'New';
    $new_status      = $updates['status'] ?? $previous_status;

    if ($new_status !== $previous_status) {
        $existing['status_history'][] = [
            'from'       => $previous_status,
            'to'         => $new_status,
            'changed_at' => date('c'),
            'by'         => $_SESSION['admin_user']['username'] ?? 'admin',
        ];
    }

    foreach ($updates as $k => $v) {
        $existing[$k] = $v;
    }
    $existing['updated_at'] = date('c');

    $meta[$ref] = $existing;
    return (bool) @file_put_contents(ADMIN_META_FILE, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

/** Retrieve all enquiries across all monthly JSONL files */
function admin_get_all_enquiries(): array
{
    $enquiries = [];
    $meta = admin_get_enquiry_metadata();

    $dir = ENQUIRY_DIR;
    $files = is_dir($dir) ? (glob($dir . DIRECTORY_SEPARATOR . 'enquiries-*.jsonl') ?: []) : [];

    // Sort files descending (newest month first)
    if (!empty($files)) {
        rsort($files);
    }

    foreach ($files as $file) {
        $handle = @fopen($file, 'r');
        if (!$handle) {
            continue;
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $item = json_decode($line, true);
            if (!is_array($item) || empty($item['reference'])) {
                continue;
            }

            $ref = $item['reference'];
            $item_meta = $meta[$ref] ?? [
                'status'         => 'New',
                'notes'          => '',
                'priority'       => 'Normal',
                'status_history' => [],
                'updated_at'     => null,
            ];

            $item['status']         = $item_meta['status'] ?? 'New';
            $item['notes']          = $item_meta['notes'] ?? '';
            $item['priority']       = $item_meta['priority'] ?? 'Normal';
            $item['status_history'] = $item_meta['status_history'] ?? [];
            $item['updated_at']     = $item_meta['updated_at'] ?? null;

            $enquiries[] = $item;
        }
        fclose($handle);
    }

    // Merge enquiries from MySQL database
    global $pdo;
    if ($pdo instanceof PDO) {
        try {
            $existing_refs = array_flip(array_filter(array_column($enquiries, 'reference')));
            $rows = $pdo->query("SELECT * FROM enquiries ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $ref = !empty($row['reference']) ? (string) $row['reference'] : ('PD-' . str_pad((string) $row['id'], 5, '0', STR_PAD_LEFT));
                if (isset($existing_refs[$ref])) {
                    continue;
                }
                $item_meta = $meta[$ref] ?? [
                    'status'         => ucfirst($row['status'] ?? 'New'),
                    'notes'          => '',
                    'priority'       => 'Normal',
                    'status_history' => [],
                    'updated_at'     => $row['updated_at'] ?? null,
                ];

                $enquiries[] = [
                    'reference'   => $ref,
                    'received_at' => $row['created_at'] ?? date('c'),
                    'name'        => $row['name'] ?? '',
                    'email'       => $row['email'] ?? '',
                    'phone'       => $row['phone'] ?? '',
                    'company'     => $row['company'] ?? '',
                    'service'     => $row['service'] ?? '',
                    'budget'      => $row['budget'] ?? '',
                    'message'     => $row['message'] ?? '',
                    'source_page' => 'website',
                    'status'      => $item_meta['status'] ?? ucfirst($row['status'] ?? 'New'),
                    'notes'       => $item_meta['notes'] ?? '',
                    'priority'    => $item_meta['priority'] ?? 'Normal',
                    'status_history' => $item_meta['status_history'] ?? [],
                    'updated_at'  => $item_meta['updated_at'] ?? null,
                ];
            }
        } catch (PDOException $e) {
            // Silently keep JSONL records if database query fails
        }
    }

    // Sort newest received_at first
    usort($enquiries, static function ($a, $b) {
        return strcmp($b['received_at'] ?? '', $a['received_at'] ?? '');
    });

    return $enquiries;
}

/** Retrieve a single enquiry by reference */
function admin_get_enquiry_by_ref(string $ref): ?array
{
    $ref = trim($ref);
    if ($ref === '') {
        return null;
    }

    $all = admin_get_all_enquiries();
    foreach ($all as $item) {
        if (strcasecmp($item['reference'], $ref) === 0) {
            return $item;
        }
    }
    return null;
}

/** Filter and search enquiries */
function admin_filter_enquiries(array $enquiries, array $filters): array
{
    $search  = trim((string) ($filters['q'] ?? ''));
    $status  = trim((string) ($filters['status'] ?? ''));
    $service = trim((string) ($filters['service'] ?? ''));
    $date_from = trim((string) ($filters['date_from'] ?? ''));
    $date_to   = trim((string) ($filters['date_to'] ?? ''));

    return array_values(array_filter($enquiries, static function ($item) use ($search, $status, $service, $date_from, $date_to) {
        if ($status !== '' && strcasecmp($item['status'] ?? '', $status) !== 0) {
            return false;
        }

        if ($service !== '' && strcasecmp($item['service'] ?? '', $service) !== 0) {
            return false;
        }

        if ($date_from !== '') {
            $recDate = substr($item['received_at'] ?? '', 0, 10);
            if ($recDate < $date_from) {
                return false;
            }
        }

        if ($date_to !== '') {
            $recDate = substr($item['received_at'] ?? '', 0, 10);
            if ($recDate > $date_to) {
                return false;
            }
        }

        if ($search !== '') {
            $haystack = mb_strtolower(
                ($item['reference'] ?? '') . ' ' .
                ($item['name'] ?? '') . ' ' .
                ($item['email'] ?? '') . ' ' .
                ($item['phone'] ?? '') . ' ' .
                ($item['company'] ?? '') . ' ' .
                ($item['message'] ?? '') . ' ' .
                ($item['notes'] ?? '')
            );
            $needle = mb_strtolower($search);
            if (!str_contains($haystack, $needle)) {
                return false;
            }
        }

        return true;
    }));
}

/** Aggregate dashboard statistics */
function admin_get_stats(array $enquiries): array
{
    $total = count($enquiries);
    $current_month = date('Y-m');
    $this_month = 0;

    $by_status = [
        'New'         => 0,
        'Contacted'   => 0,
        'In Progress' => 0,
        'Closed'      => 0,
    ];

    $by_service = [];
    $recent = array_slice($enquiries, 0, 8);

    foreach ($enquiries as $item) {
        if (str_starts_with($item['received_at'] ?? '', $current_month)) {
            $this_month++;
        }

        $st = $item['status'] ?? 'New';
        if (isset($by_status[$st])) {
            $by_status[$st]++;
        } else {
            $by_status[$st] = 1;
        }

        $svc = $item['service'] ?? 'Other';
        $by_service[$svc] = ($by_service[$svc] ?? 0) + 1;
    }

    arsort($by_service);

    return [
        'total'      => $total,
        'this_month' => $this_month,
        'by_status'  => $by_status,
        'by_service' => $by_service,
        'recent'     => $recent,
    ];
}

/** Generate status badge HTML */
function admin_status_badge(string $status): string
{
    $status = trim($status);
    $class = match (strtolower($status)) {
        'new'         => 'badge--new',
        'contacted'   => 'badge--contacted',
        'in progress' => 'badge--in-progress',
        'closed'      => 'badge--closed',
        default       => 'badge--default',
    };

    return '<span class="status-badge ' . $class . '">' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</span>';
}

/** Set or get flash message */
function admin_set_flash(string $type, string $message): void
{
    $_SESSION['admin_flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

function admin_get_flash(): ?array
{
    if (isset($_SESSION['admin_flash'])) {
        $flash = $_SESSION['admin_flash'];
        unset($_SESSION['admin_flash']);
        return $flash;
    }
    return null;
}
