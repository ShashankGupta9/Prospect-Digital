<?php
/**
 * Prospect Digital — Admin Logout
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/admin-functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['admin_user'], $_SESSION['admin_last_activity']);
admin_set_flash('success', 'You have been successfully signed out.');
header('Location: ' . url('admin/login.php'));
exit;
