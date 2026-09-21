<?php
/**
 * Prospect Digital — Admin Settings & System Diagnostics
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$page_title = 'Settings & Diagnostics';
$active_nav = 'settings';
$csrf_token = admin_csrf_token();

$config = admin_config();
$users = $config['users'] ?? [];

// Password change handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf($csrf)) {
        admin_set_flash('error', 'CSRF validation failed.');
    } else {
        $curr_pass = (string) ($_POST['current_password'] ?? '');
        $new_pass  = (string) ($_POST['new_password'] ?? '');
        $conf_pass = (string) ($_POST['confirm_password'] ?? '');

        $username = $current_admin['username'];
        $user_record = $users[$username] ?? null;

        if (!$user_record || !password_verify($curr_pass, $user_record['password_hash'])) {
            admin_set_flash('error', 'Current password is incorrect.');
        } elseif (strlen($new_pass) < 8) {
            admin_set_flash('error', 'New password must be at least 8 characters long.');
        } elseif ($new_pass !== $conf_pass) {
            admin_set_flash('error', 'New password and confirmation do not match.');
        } else {
            $config['users'][$username]['password_hash'] = password_hash($new_pass, PASSWORD_BCRYPT);
            if (admin_save_config($config)) {
                admin_set_flash('success', 'Your password has been changed successfully.');
            } else {
                admin_set_flash('error', 'Failed to save new password. Check file permissions.');
            }
        }
        header('Location: ' . url('admin/settings.php'));
        exit;
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Settings &amp; Diagnostics</h1>
    <p class="page-subtitle">Configure administrator access and review server environment status.</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
  <!-- Security & Password -->
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Change Password</h2>
    </div>
    <div class="card-body">
      <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="action" value="change_password">

        <div class="form-group">
          <label class="form-label" for="current_password">Current Password</label>
          <input type="password" id="current_password" name="current_password" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="new_password">New Password</label>
          <input type="password" id="new_password" name="new_password" class="form-input" required minlength="8" placeholder="Minimum 8 characters">
        </div>

        <div class="form-group">
          <label class="form-label" for="confirm_password">Confirm New Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-input" required minlength="8">
        </div>

        <button type="submit" class="btn btn--brand">
          Update Password
        </button>
      </form>
    </div>
  </div>

 

<?php require __DIR__ . '/includes/footer.php'; ?>
