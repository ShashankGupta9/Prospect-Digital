<?php
/**
 * Prospect Digital — Client Account Dashboard
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/user-auth.php';

$user = user_require_login();

// Query all enquiries submitted by this user's email
$user_enquiries = [];
global $pdo;
if ($pdo instanceof PDO) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM enquiries WHERE email = :email ORDER BY id DESC");
        $stmt->execute([':email' => $user['email']]);
        $user_enquiries = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Failed to fetch user enquiries: ' . $e->getMessage());
    }
}

$flash = take_flash();

$page_title       = 'Client Dashboard — ' . $user['name'];
$page_description = 'View your active consultation requests and account details.';
$body_class       = 'page-dashboard';

require __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding: 4rem 0; min-height: 80vh;">
  <div class="container">
    
    <?php if ($flash && !empty($flash['message'])): ?>
      <div class="form-alert form-alert--success" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 2rem; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;" data-reveal>
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px; color: #059669; flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
        <span><?= e($flash['message']) ?></span>
      </div>
    <?php endif; ?>

    <!-- User Welcome Banner -->
    <div class="card interactive-card" style="background: var(--glass-card-grad); backdrop-filter: var(--glass-blur); -webkit-backdrop-filter: var(--glass-blur); border: 1px solid var(--glass-border); border-radius: 16px; padding: 2rem 2.5rem; margin-bottom: 2.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;" data-reveal>
      <div>
        <p class="eyebrow" style="margin-bottom: 0.35rem;">MY ACCOUNT</p>
        <h1 style="font-size: 2rem; font-weight: 800; color: var(--ink); letter-spacing: -0.02em;">
          Hello, <?= e($user['name']) ?>
        </h1>
        <p style="color: var(--text2); font-size: 0.95rem; margin-top: 0.25rem;">
          <?= e($user['email']) ?> <?= $user['phone'] ? ' &bull; ' . e($user['phone']) : '' ?>
        </p>
      </div>

      <div style="display: flex; gap: 0.75rem;">
        <a href="<?= e(url('contact')) ?>" class="btn btn--brand">New Consultation Request</a>
        <a href="<?= e(url('logout.php')) ?>" class="btn btn--outline">Sign Out</a>
      </div>
    </div>

    <!-- User Enquiries List -->
    <div class="card" style="background: var(--glass-card-grad); backdrop-filter: var(--glass-blur); border: 1px solid var(--glass-border); border-radius: 16px; overflow: hidden;" data-reveal>
      <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--ink);">Your Consultation &amp; Project Inquiries</h2>
        <span style="font-size: 0.85rem; font-weight: 700; color: var(--brand); background: var(--brand-tint); padding: 4px 10px; border-radius: 999px;">
          <?= count($user_enquiries) ?> Total
        </span>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
          <thead>
            <tr style="background: rgba(248, 250, 252, 0.6); text-align: left; border-bottom: 1px solid var(--line);">
              <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text3); font-size: 0.8rem; text-transform: uppercase;">Service</th>
              <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text3); font-size: 0.8rem; text-transform: uppercase;">Company</th>
              <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text3); font-size: 0.8rem; text-transform: uppercase;">Budget</th>
              <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text3); font-size: 0.8rem; text-transform: uppercase;">Date Submitted</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($user_enquiries)): ?>
              <tr>
                <td colspan="4" style="text-align: center; padding: 3rem 1.5rem; color: var(--text3);">
                  You have not submitted any enquiries yet. <a href="<?= e(url('contact')) ?>" style="color: var(--brand); font-weight: 700;">Submit your first request &#8594;</a>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($user_enquiries as $enq): ?>
                <tr style="border-bottom: 1px solid var(--line);">
                  <td style="padding: 1.25rem 1.5rem; font-weight: 700; color: var(--ink);"><?= e($enq['service']) ?></td>
                  <td style="padding: 1.25rem 1.5rem; color: var(--text2);"><?= e($enq['company'] ?: 'Individual') ?></td>
                  <td style="padding: 1.25rem 1.5rem; color: var(--text2);"><?= e($enq['budget'] ?: 'Flexible') ?></td>
                  <td style="padding: 1.25rem 1.5rem; color: var(--text3); font-size: 0.85rem;">
                    <?= !empty($enq['created_at']) ? date('M d, Y', strtotime($enq['created_at'])) : date('M d, Y') ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
