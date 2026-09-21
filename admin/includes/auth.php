<?php
/**
 * Prospect Digital — Admin Authentication Guard
 * ---------------------------------------------------------------------------
 * Included at the top of every protected admin page.
 * Validates session, inactivity timeout, and user existence.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/admin-functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = admin_config();
$timeout = (int) ($config['settings']['session_timeout'] ?? 7200);

$user = $_SESSION['admin_user'] ?? null;
$last_activity = $_SESSION['admin_last_activity'] ?? 0;

if (!$user || !isset($user['username'])) {
    $redirect = $_SERVER['REQUEST_URI'] ?? '';
    $loginUrl = url('admin/login.php') . ($redirect ? '?return=' . urlencode($redirect) : '');
    header('Location: ' . $loginUrl);
    exit;
}

// Session timeout check
if ($last_activity > 0 && (time() - $last_activity) > $timeout) {
    unset($_SESSION['admin_user'], $_SESSION['admin_last_activity']);
    admin_set_flash('error', 'Your session has expired due to inactivity. Please sign in again.');
    header('Location: ' . url('admin/login.php'));
    exit;
}

$_SESSION['admin_last_activity'] = time();

$current_admin = $user;
