<?php
/**
 * Prospect Digital — User Logout
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/user-auth.php';

user_logout();
set_flash('success', 'You have been signed out.');
header('Location: ' . url('login'));
exit;
