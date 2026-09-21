<?php
/**
 * Prospect Digital — User Sign In
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/user-auth.php';

if (current_user()) {
    header('Location: ' . url('dashboard'));
    exit;
}

$error      = '';
$email      = '';
$return_url = $_POST['return'] ?? $_GET['return'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        $error = 'Security session expired. Please try again.';
    } else {
        $email    = (string) ($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $result = user_login($email, $password);
        if ($result['ok']) {
            $dest = url('dashboard');
            if ($return_url !== '' && str_starts_with($return_url, BASE_URL)) {
                $dest = $return_url;
            }
            header('Location: ' . $dest);
            exit;
        }
        $error = $result['error'] ?? 'Sign in failed.';
    }
}

$flash = take_flash();

$page_title       = 'Client Sign In — Prospect Digital';
$page_description = 'Sign in to access your Prospect Digital account, enquiries, and project milestones.';
$body_class       = 'page-auth';

require __DIR__ . '/includes/header.php';
?>

<section class="section auth-section" style="min-height: 80vh; display: flex; align-items: center; padding: 4rem 0;">
  <div class="container" style="max-width: 460px;">
    
    <div class="card interactive-card" style="background: var(--glass-card-grad); backdrop-filter: var(--glass-blur); -webkit-backdrop-filter: var(--glass-blur); border: 1px solid var(--glass-border); box-shadow: var(--glass-shadow); border-radius: var(--radius-lg, 16px); padding: 2.75rem 2.25rem;" data-reveal>
      
      <div style="text-align: center; margin-bottom: 2rem;">
        <span class="eyebrow" style="margin-bottom: 0.5rem; display: inline-block;">WELCOME BACK</span>
        <h1 style="font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em; color: var(--ink);">Sign In</h1>
        <p style="color: var(--text2); font-size: 0.95rem; margin-top: 0.35rem;">Enter your credentials to manage your account.</p>
      </div>

      <?php if ($flash && !empty($flash['message'])): ?>
        <div class="form-alert form-alert--success" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.85rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; color: #059669; flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
          <span><?= e($flash['message']) ?></span>
        </div>
      <?php endif; ?>

      <?php if ($error !== ''): ?>
        <div class="form-alert form-alert--error" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 0.85rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <?php if ($return_url !== ''): ?>
          <input type="hidden" name="return" value="<?= e($return_url) ?>">
        <?php endif; ?>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label" for="email" style="font-weight: 700; font-size: 0.85rem; color: var(--ink); margin-bottom: 0.4rem; display: block;">Email Address</label>
          <input type="email" id="email" name="email" class="form-input" required placeholder="name@company.com" value="<?= e($email) ?>" autofocus style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--line); border-radius: 8px; font-size: 0.95rem; background: rgba(255, 255, 255, 0.9);">
        </div>

        <div class="form-group" style="margin-bottom: 1.75rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label class="form-label" for="password" style="font-weight: 700; font-size: 0.85rem; color: var(--ink); margin-bottom: 0;">Password</label>
          </div>
          <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••••••" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--line); border-radius: 8px; font-size: 0.95rem; background: rgba(255, 255, 255, 0.9);">
        </div>

        <button type="submit" class="btn btn--brand btn--lg" style="width: 100%; justify-content: center;">
          Sign In <?= icon('arrow', 'icon btn__icon') ?>
        </button>

        <div style="margin-top: 1.75rem; text-align: center; font-size: 0.9rem; color: var(--text2);">
          Don't have an account yet? <a href="<?= e(url('signup' . ($return_url !== '' ? '?return=' . urlencode($return_url) : ''))) ?>" style="color: var(--brand); font-weight: 700;">Sign up free &#8594;</a>
        </div>
      </form>

    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
