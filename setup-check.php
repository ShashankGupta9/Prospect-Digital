<?php
/**
 * Prospect Digital — Hostinger & Server Environment Diagnostics
 * ---------------------------------------------------------------------------
 * Run this file in your browser: https://yourdomain.com/setup-check.php
 * or run it from the command line: php setup-check.php
 * 
 * NOTE: For security, delete this file from your live server after setup.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

$is_cli = (PHP_SAPI === 'cli');

$checks = [];

// 1. PHP Version Check
$php_version = PHP_VERSION;
$php_ok = version_compare($php_version, '8.0.0', '>=');
$checks[] = [
    'title'       => 'PHP Version',
    'status'      => $php_ok ? 'PASS' : 'FAIL',
    'details'     => "Installed: PHP {$php_version} (Hostinger recommended: PHP 8.1 or 8.2)",
    'recommended' => 'PHP >= 8.0',
];

// 2. Critical Extensions
$extensions = [
    'pdo'        => 'PHP Data Objects core',
    'pdo_mysql'  => 'MySQL PDO driver for database operations',
    'json'       => 'JSON parser for config & enquiries log',
    'mbstring'   => 'Multi-byte string handling for UTF-8',
    'openssl'    => 'OpenSSL for secure HTTPS and SMTP email',
    'curl'       => 'cURL for external HTTP API requests',
    'fileinfo'   => 'Fileinfo for secure MIME-type upload validation',
    'gd'         => 'GD library for store image processing',
];

foreach ($extensions as $ext => $desc) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? 'PASS' : ($ext === 'gd' ? 'INFO' : 'FAIL');
    $details = $loaded 
        ? "Loaded ({$desc})" 
        : ($ext === 'gd' 
            ? "Optional ({$desc}). Enabled by default on Hostinger; optional for local testing." 
            : "Missing ({$desc})");

    $checks[] = [
        'title'       => "Extension: {$ext}" . ($ext === 'gd' ? ' (Optional)' : ''),
        'status'      => $status,
        'details'     => $details,
        'recommended' => $ext === 'gd' ? 'Pre-enabled on Hostinger' : 'Enabled in PHP configuration',
    ];
}

// 3. Writable Storage Folders
$folders = [
    'data'                => __DIR__ . '/data',
    'data/enquiries'      => __DIR__ . '/data/enquiries',
    'data/admin'          => __DIR__ . '/data/admin',
    'assets/images/store' => __DIR__ . '/assets/images/store',
];

foreach ($folders as $label => $path) {
    if (!is_dir($path)) {
        @mkdir($path, 0775, true);
    }
    $exists = is_dir($path);
    $writable = $exists && is_writable($path);
    $checks[] = [
        'title'       => "Folder Permission: {$label}",
        'status'      => $writable ? 'PASS' : ($exists ? 'FAIL' : 'WARN'),
        'details'     => $writable ? "Exists and is writable (0775/0755)" : ($exists ? "Exists but is NOT writable. Run: chmod 775 {$label}" : "Folder missing. Created automatically if possible."),
        'recommended' => 'Writable by web server process',
    ];
}

// 4. Database Connection & Tables
$db_connected = false;
$db_error = null;
$table_count = 0;
$missing_tables = [];

$expected_tables = [
    'enquiries',
    'users',
    'admins',
    'admin_activity_logs',
    'pd_admin_users',
    'pd_leads',
    'pd_newsletter',
    'pd_site_ctas',
    'store_categories',
    'store_products',
    'store_product_images',
    'store_product_specs',
    'store_product_features',
    'store_customers',
    'store_orders',
    'store_order_items',
];

if (db_is_connected()) {
    $db_connected = true;
    try {
        global $pdo;
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $tables_lower = array_map('strtolower', $tables);
        $table_count = count($tables);

        foreach ($expected_tables as $tbl) {
            if (!in_array(strtolower($tbl), $tables_lower, true)) {
                $missing_tables[] = $tbl;
            }
        }
    } catch (Exception $e) {
        $db_error = $e->getMessage();
    }
} else {
    $db_error = $GLOBALS['db_connection_error'] ?? 'Could not connect with current credentials.';
}

$checks[] = [
    'title'       => 'MySQL Database Connection',
    'status'      => $db_connected ? 'PASS' : 'FAIL',
    'details'     => $db_connected
        ? "Connected to database `" . DB_NAME . "` on `" . DB_HOST . "` as `" . DB_USER . "`"
        : "Failed: " . ($db_error ?: 'Unknown error') . ". Check DB_HOST, DB_NAME, DB_USER, DB_PASS in includes/config.php or .env",
    'recommended' => 'Valid Hostinger database credentials',
];

if ($db_connected) {
    $tables_ok = empty($missing_tables);
    $checks[] = [
        'title'       => 'Database Tables Check',
        'status'      => $tables_ok ? 'PASS' : 'WARN',
        'details'     => $tables_ok
            ? "All {$table_count} expected tables found in database."
            : "Missing " . count($missing_tables) . " tables: " . implode(', ', $missing_tables) . ". Import database.sql via Hostinger phpMyAdmin.",
        'recommended' => 'Import database.sql into phpMyAdmin',
    ];
}

// 5. Environment Information
$checks[] = [
    'title'       => 'Site URL & Base Path',
    'status'      => 'INFO',
    'details'     => "SITE_URL: " . SITE_URL . " | BASE_URL: '" . BASE_URL . "' | Operating System: " . PHP_OS,
    'recommended' => 'SITE_URL matches your live domain',
];

$checks[] = [
    'title'       => 'Debug Mode',
    'status'      => DEBUG_MODE ? 'WARN' : 'PASS',
    'details'     => DEBUG_MODE ? "DEBUG_MODE is TRUE (Shows full PHP errors. Set APP_DEBUG=false or DEBUG_MODE=false before public launch)." : "DEBUG_MODE is FALSE (Errors safely logged, not shown to visitors).",
    'recommended' => 'FALSE in production',
];

// CLI Output
if ($is_cli) {
    echo "========================================================\n";
    echo " Prospect Digital — Hostinger Setup Diagnostics\n";
    echo "========================================================\n\n";

    foreach ($checks as $c) {
        $badge = str_pad("[{$c['status']}]", 8);
        echo "{$badge} {$c['title']}\n";
        echo "         {$c['details']}\n\n";
    }

    echo "========================================================\n";
    exit;
}

// Browser HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hostinger Setup Diagnostics — Prospect Digital</title>
  <style>
    :root {
      --bg: #090d16;
      --card: rgba(255, 255, 255, 0.05);
      --border: rgba(255, 255, 255, 0.1);
      --text: #f1f5f9;
      --muted: #94a3b8;
      --pass: #10b981;
      --fail: #ef4444;
      --warn: #f59e0b;
      --info: #3b82f6;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
      padding: 2.5rem 1rem;
    }
    .container { max-width: 800px; margin: 0 auto; }
    .header { margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; }
    .header h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem; }
    .header p { color: var(--muted); font-size: 0.95rem; }
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      backdrop-filter: blur(10px);
    }
    .check-item {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      padding: 1rem 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .check-item:last-child { border-bottom: none; }
    .badge {
      font-size: 0.75rem;
      font-weight: 700;
      padding: 0.3rem 0.65rem;
      border-radius: 6px;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      flex-shrink: 0;
    }
    .badge--PASS { background: rgba(16, 185, 129, 0.2); color: var(--pass); border: 1px solid var(--pass); }
    .badge--FAIL { background: rgba(239, 68, 68, 0.2); color: var(--fail); border: 1px solid var(--fail); }
    .badge--WARN { background: rgba(245, 158, 11, 0.2); color: var(--warn); border: 1px solid var(--warn); }
    .badge--INFO { background: rgba(59, 130, 246, 0.2); color: var(--info); border: 1px solid var(--info); }
    .content { flex-grow: 1; }
    .content-title { font-weight: 600; font-size: 1rem; margin-bottom: 0.25rem; }
    .content-details { color: var(--muted); font-size: 0.88rem; }
    .quick-links { display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap; }
    .btn {
      display: inline-block;
      padding: 0.75rem 1.25rem;
      background: #e11d48;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      border-radius: 8px;
      font-size: 0.9rem;
    }
    .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid var(--border); }
    .warning-box {
      background: rgba(245, 158, 11, 0.1);
      border: 1px solid var(--warn);
      padding: 1rem 1.25rem;
      border-radius: 8px;
      color: #fde68a;
      font-size: 0.88rem;
      margin-top: 2rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Prospect Digital — Hostinger Setup Diagnostics</h1>
      <p>Automated verification of PHP environment, extensions, writable folders, and database connection.</p>
    </div>

    <div class="card">
      <?php foreach ($checks as $c): ?>
        <div class="check-item">
          <span class="badge badge--<?= e($c['status']) ?>"><?= e($c['status']) ?></span>
          <div class="content">
            <div class="content-title"><?= e($c['title']) ?></div>
            <div class="content-details"><?= e($c['details']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="quick-links">
      <a href="<?= e(url('download-db.php')) ?>" class="btn" style="background: #10b981;">📥 Download Database (.sql)</a>
      <a href="<?= e(url('')) ?>" class="btn">Go to Homepage</a>
      <a href="<?= e(url('admin/login.php')) ?>" class="btn btn-secondary">Admin Login</a>
      <a href="<?= e(url('test-db.php')) ?>" class="btn btn-secondary">Test DB Connection</a>
    </div>

    <div class="warning-box">
      <strong>Important Security Notice:</strong> Once your site and database are working on Hostinger, please delete this <code>setup-check.php</code> file from your server.
    </div>
  </div>
</body>
</html>
