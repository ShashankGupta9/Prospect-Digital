<?php
/**
 * Prospect Digital — User Sign Up
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/user-auth.php';

if (current_user()) {
    header('Location: ' . url('dashboard'));
    exit;
}

$errors     = [];
$name       = '';
$email      = '';
$phone      = '';
$return_url = $_POST['return'] ?? $_GET['return'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        $errors['general'] = 'Session expired. Please refresh the page and try again.';
    } else {
        $name     = (string) ($_POST['name'] ?? '');
        $email    = (string) ($_POST['email'] ?? '');
        $phone    = (string) ($_POST['phone'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm  = (string) ($_POST['confirm_password'] ?? '');

        if ($password !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match.';
        } else {
            $result = user_register($name, $email, $phone, $password);
            if ($result['ok']) {
                set_flash('success', 'Welcome to Prospect Digital! Your account has been created.');
                $dest = url('dashboard');
                if ($return_url !== '' && str_starts_with($return_url, BASE_URL)) {
                    $dest = $return_url;
                }
                header('Location: ' . $dest);
                exit;
            }
            $errors = $result['errors'] ?? [];
        }
    }
}

$page_title       = 'Create Your Account — Prospect Digital';
$page_description = 'Sign up for a client account with Prospect Digital to manage projects, track enquiries and get support.';
$body_class       = 'page-auth';

require __DIR__ . '/includes/header.php';
?>

<section class="section auth-section" style="min-height: 85vh; display: flex; align-items: center; padding: 4rem 0;">
  <div class="container" style="max-width: 520px;">
    
    <div class="card interactive-card" style="background: var(--glass-card-grad); backdrop-filter: var(--glass-blur); -webkit-backdrop-filter: var(--glass-blur); border: 1px solid var(--glass-border); box-shadow: var(--glass-shadow); border-radius: var(--radius-lg, 16px); padding: 2.5rem 2rem;" data-reveal>
      
      <div style="text-align: center; margin-bottom: 2rem;">
        <span class="eyebrow" style="margin-bottom: 0.5rem; display: inline-block;">JOIN PROSPECT DIGITAL</span>
        <h1 style="font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em; color: var(--ink);">Create Account</h1>
        <p style="color: var(--text2); font-size: 0.95rem; margin-top: 0.35rem;">Track your project proposals and consult directly with our team.</p>
      </div>

      <?php if (!empty($errors['general'])): ?>
        <div class="form-alert form-alert--error" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 0.85rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
          <?= e($errors['general']) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <?php if ($return_url !== ''): ?>
          <input type="hidden" name="return" value="<?= e($return_url) ?>">
        <?php endif; ?>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label" for="name" style="font-weight: 700; font-size: 0.85rem; color: var(--ink); margin-bottom: 0.4rem; display: block;">Full Name</label>
          <input type="text" id="name" name="name" class="form-input" required placeholder="e.g. Rahul Verma" value="<?= e($name) ?>" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid <?= isset($errors['name']) ? '#ef4444' : 'var(--line)' ?>; border-radius: 8px; font-size: 0.95rem; background: rgba(255, 255, 255, 0.9);">
          <?php if (isset($errors['name'])): ?><p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label" for="email" style="font-weight: 700; font-size: 0.85rem; color: var(--ink); margin-bottom: 0.4rem; display: block;">Email Address</label>
          <input type="email" id="email" name="email" class="form-input" required placeholder="name@company.com" value="<?= e($email) ?>" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid <?= isset($errors['email']) ? '#ef4444' : 'var(--line)' ?>; border-radius: 8px; font-size: 0.95rem; background: rgba(255, 255, 255, 0.9);">
          <?php if (isset($errors['email'])): ?><p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label" for="phone" style="font-weight: 700; font-size: 0.85rem; color: var(--ink); margin-bottom: 0.4rem; display: block;">Phone Number</label>
          <input type="tel" id="phone" name="phone" class="form-input" placeholder="+91 98765-43210" value="<?= e($phone) ?>" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid <?= isset($errors['phone']) ? '#ef4444' : 'var(--line)' ?>; border-radius: 8px; font-size: 0.95rem; background: rgba(255, 255, 255, 0.9);">
          <?php if (isset($errors['phone'])): ?><p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;"><?= e($errors['phone']) ?></p><?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label" for="password" style="font-weight: 700; font-size: 0.85rem; color: var(--ink); margin-bottom: 0.4rem; display: block;">Password (Min 8 characters)</label>
          <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••••••" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid <?= isset($errors['password']) ? '#ef4444' : 'var(--line)' ?>; border-radius: 8px; font-size: 0.95rem; background: rgba(255, 255, 255, 0.9);">
          <?php if (isset($errors['password'])): ?><p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;"><?= e($errors['password']) ?></p><?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom: 1.75rem;">
          <label class="form-label" for="confirm_password" style="font-weight: 700; font-size: 0.85rem; color: var(--ink); margin-bottom: 0.4rem; display: block;">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-input" required placeholder="••••••••••••" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid <?= isset($errors['confirm_password']) ? '#ef4444' : 'var(--line)' ?>; border-radius: 8px; font-size: 0.95rem; background: rgba(255, 255, 255, 0.9);">
          <?php if (isset($errors['confirm_password'])): ?><p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;"><?= e($errors['confirm_password']) ?></p><?php endif; ?>
        </div>

        <button type="submit" class="btn btn--brand btn--lg" style="width: 100%; justify-content: center;">
          Create My Account <?= icon('arrow', 'icon btn__icon') ?>
        </button>

        <div style="margin-top: 1.75rem; text-align: center; font-size: 0.9rem; color: var(--text2);">
          Already have an account? <a href="<?= e(url('login' . ($return_url !== '' ? '?return=' . urlencode($return_url) : ''))) ?>" style="color: var(--brand); font-weight: 700;">Sign in here &#8594;</a>
        </div>
      </form>

    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
