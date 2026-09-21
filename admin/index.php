<?php
/**
 * Prospect Digital — Admin Dashboard Overview
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$page_title = 'Dashboard Overview';
$active_nav = 'dashboard';

$all_enquiries = admin_get_all_enquiries();
$stats = admin_get_stats($all_enquiries);

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Welcome back, <?= htmlspecialchars($current_admin['name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="page-subtitle">Here is a real-time summary of leads, customer enquiries and platform activity.</p>
  </div>
  <div style="display:flex; gap:0.75rem;">
    <a href="<?= url('admin/export.php') ?>" class="btn btn--secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export CSV
    </a>
    <a href="<?= url('admin/enquiries.php') ?>" class="btn btn--brand">
      View All Enquiries (<?= $stats['total'] ?>)
    </a>
  </div>
</div>

<!-- KPI Stats Grid -->
<div class="stats-grid">
  <div class="stat-card stat-card--total">
    <div>
      <div class="stat-card__label">Total Submissions</div>
      <div class="stat-card__val"><?= number_format($stats['total']) ?></div>
    </div>
    <div class="stat-card__icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
  </div>

  <div class="stat-card stat-card--month">
    <div>
      <div class="stat-card__label">This Month (<?= date('M Y') ?>)</div>
      <div class="stat-card__val"><?= number_format($stats['this_month']) ?></div>
    </div>
    <div class="stat-card__icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
  </div>

  <div class="stat-card stat-card--new">
    <div>
      <div class="stat-card__label">New / Uncontacted</div>
      <div class="stat-card__val"><?= number_format($stats['by_status']['New'] ?? 0) ?></div>
    </div>
    <div class="stat-card__icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
  </div>

  <div class="stat-card stat-card--closed">
    <div>
      <div class="stat-card__label">Closed / Won</div>
      <div class="stat-card__val"><?= number_format($stats['by_status']['Closed'] ?? 0) ?></div>
    </div>
    <div class="stat-card__icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
  <!-- Recent Enquiries -->
  <div class="card" style="margin-bottom: 0;">
    <div class="card-header">
      <h2 class="card-title">Recent Enquiries</h2>
      <a href="<?= url('admin/enquiries.php') ?>" class="btn btn--outline btn--sm">View All &#8594;</a>
    </div>
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Contact</th>
            <th>Service Requested</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($stats['recent'])): ?>
            <tr>
              <td colspan="6" style="text-align:center; padding: 2.5rem; color: var(--admin-text-muted);">
                No customer enquiries have been submitted yet.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($stats['recent'] as $enq): ?>
              <tr>
                <td class="primary">
                  <a href="<?= url('admin/enquiry.php?ref=' . urlencode($enq['reference'])) ?>" style="font-family:monospace; color:var(--admin-brand);">
                    <?= htmlspecialchars($enq['reference'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </td>
                <td>
                  <div style="font-weight:600; color:var(--admin-text-primary);"><?= htmlspecialchars($enq['name'], ENT_QUOTES, 'UTF-8') ?></div>
                  <div style="font-size:12px; color:var(--admin-text-muted);"><?= htmlspecialchars($enq['email'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars($enq['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                </td>
                <td><?= htmlspecialchars($enq['service'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <span title="<?= htmlspecialchars($enq['received_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?= date('M d, Y H:i', strtotime($enq['received_at'] ?? 'now')) ?>
                  </span>
                </td>
                <td><?= admin_status_badge($enq['status'] ?? 'New') ?></td>
                <td>
                  <a href="<?= url('admin/enquiry.php?ref=' . urlencode($enq['reference'])) ?>" class="btn btn--outline btn--sm">
                    Open
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Service Demand Breakdown -->
  <div class="card" style="margin-bottom: 0;">
    <div class="card-header">
      <h2 class="card-title">Enquiries by Service</h2>
    </div>
    <div class="card-body">
      <?php if (empty($stats['by_service'])): ?>
        <p style="color:var(--admin-text-muted); text-align:center; padding: 1.5rem 0;">No services requested yet.</p>
      <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:1rem;">
          <?php foreach ($stats['by_service'] as $serviceName => $count): 
            $pct = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0;
          ?>
            <div>
              <div style="display:flex; justify-content:space-between; margin-bottom: 0.35rem; font-size: 13px;">
                <span style="font-weight:500;"><?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?></span>
                <span style="color:var(--admin-text-muted); font-weight:600;"><?= $count ?> (<?= $pct ?>%)</span>
              </div>
              <div style="height:6px; background:var(--admin-surface); border-radius:999px; overflow:hidden;">
                <div style="width: <?= $pct ?>%; height:100%; background: var(--admin-brand); border-radius:999px;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
