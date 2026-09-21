<?php
/**
 * Prospect Digital — Admin Login
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/admin-functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (!empty($_SESSION['admin_user'])) {
    header('Location: ' . url('admin/index.php'));
    exit;
}

$error = '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$rate_check = admin_check_rate_limit($ip);
$return_url = $_GET['return'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!$rate_check['allowed']) {
        $minutes = ceil($rate_check['remaining_seconds'] / 60);
        $error = "Too many failed login attempts. Please try again in {$minutes} minutes.";
    } elseif (!admin_verify_csrf($csrf)) {
        $error = 'Security validation failed (CSRF token invalid or expired). Please try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $user = admin_authenticate($username, $password);
        if ($user) {
            // Successful authentication
            session_regenerate_id(true);
            admin_reset_failed_logins($ip);
            $_SESSION['admin_user'] = [
                'username' => $user['username'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'role'     => $user['role'],
            ];
            $_SESSION['admin_last_activity'] = time();

            // Safe return URL validation
            $redirect = url('admin/index.php');
            if ($return_url !== '' && str_starts_with($return_url, BASE_URL . '/admin')) {
                $redirect = $return_url;
            }

            header('Location: ' . $redirect);
            exit;
        } else {
            admin_record_failed_login($ip);
            $error = 'Invalid administrator credentials. Please check your username and password.';
        }
    }
}

$csrf_token = admin_csrf_token();
$flash = admin_get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Sign In — Prospect Digital</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= url('admin/assets/css/admin.css') ?>?v=<?= filemtime(__DIR__ . '/assets/css/admin.css') ?>">
</head>
<body class="admin-body">

<div class="admin-login-wrapper">
  <div class="login-box">
    <div class="login-box__header">
      <div class="login-box__logo">P</div>
      <h1 class="login-box__title">Prospect Console</h1>
      <p class="login-box__subtitle">Sign in to access leads, enquiries and site settings</p>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert--<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <div class="alert alert--error">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input class="form-input" type="text" id="username" name="username" required autofocus placeholder="e.g. admin" value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-input" type="password" id="password" name="password" required placeholder="••••••••••••">
      </div>

      <button class="btn btn--brand" type="submit" style="width:100%; margin-top:0.75rem; padding: 0.75rem;">
        Sign In to Dashboard
      </button>
    </form>

    <div style="margin-top: 2rem; text-align: center; font-size: 12px; color: var(--admin-text-muted);">
      <a href="<?= url('/') ?>" style="color: var(--admin-brand);">&#8592; Back to public website</a>
    </div>
  </div>
</div>

</body>
</html>
