<?php
/**
 * Prospect Digital — User Authentication Library
 * ---------------------------------------------------------------------------
 * Handles customer/client account registration, verification, and sessions.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
/**
 * Guarantee that the users table exists in the database
 */
function ensure_users_table(PDO $pdo): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(120) NOT NULL,
                `email` VARCHAR(190) NOT NULL UNIQUE,
                `phone` VARCHAR(40) DEFAULT NULL,
                `password` VARCHAR(255) NOT NULL,
                `status` VARCHAR(20) DEFAULT 'active',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } catch (PDOException $e) {
        error_log('Error ensuring users table: ' . $e->getMessage());
    }
}

/**
 * Register a new user
 */
function user_register(string $name, string $email, string $phone, string $password): array
{
    global $pdo;

    ensure_users_table($pdo);


    $name     = clean_text($name, 120);
    $email    = strtolower(clean_text($email, 190));
    $phone    = clean_text($phone, 40);
    $password = trim($password);

    $errors = [];

    if (mb_strlen($name) < 2) {
        $errors['name'] = 'Please enter your full name.';
    }

    if (!valid_email($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($phone !== '' && !valid_phone($phone)) {
        $errors['phone'] = 'Please enter a valid phone number (8–15 digits).';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    }

    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    // Check if email already registered
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            return ['ok' => false, 'errors' => ['email' => 'An account with this email address already exists.']];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $insert = $pdo->prepare("
            INSERT INTO users (name, email, phone, password, status, created_at)
            VALUES (:name, :email, :phone, :password, 'active', NOW())
        ");
        $insert->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':phone'    => $phone,
            ':password' => $hash,
        ]);

        $userId = (int) $pdo->lastInsertId();

        // Auto login after registration
        user_create_session([
            'id'    => $userId,
            'name'  => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        return ['ok' => true];

    } catch (PDOException $e) {
        error_log('User registration error: ' . $e->getMessage());
        return ['ok' => false, 'errors' => ['general' => 'Failed to create account. Please try again later.']];
    }
}

/**
 * Authenticate existing user
 */
function user_login(string $email, string $password): array
{
    global $pdo;

    ensure_users_table($pdo);

    $email    = strtolower(trim($email));
    $password = trim($password);

    if ($email === '' || $password === '') {
        return ['ok' => false, 'error' => 'Please enter both email and password.'];
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['ok' => false, 'error' => 'Invalid email address or password.'];
        }

        if (($user['status'] ?? 'active') !== 'active') {
            return ['ok' => false, 'error' => 'Your account is currently inactive. Please contact support.'];
        }

        user_create_session($user);
        return ['ok' => true];

    } catch (PDOException $e) {
        error_log('User login error: ' . $e->getMessage());
        return ['ok' => false, 'error' => 'Database error occurred. Please try again.'];
    }
}

/**
 * Set user session data safely
 */
function user_create_session(array $user): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!headers_sent()) {
        session_regenerate_id(true);
    }

    $_SESSION['user'] = [
        'id'    => (int) $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'] ?? '',
    ];
}

/**
 * Get currently logged-in user or null
 */
function current_user(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user'] ?? null;
}

/**
 * Log the user out
 */
function user_logout(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['user']);
}

/**
 * Protect customer pages
 */
function user_require_login(): array
{
    $user = current_user();
    if (!$user) {
        $redirect = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: ' . url('login') . ($redirect ? '?return=' . urlencode($redirect) : ''));
        exit;
    }
    return $user;
}
