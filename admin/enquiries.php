<?php
/**
 * Prospect Digital — Enquiries CRM Listing
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$page_title = 'Customer Enquiries';
$active_nav = 'enquiries';

$all_enquiries = admin_get_all_enquiries();

// Filter parameters
$filters = [
    'q'         => $_GET['q'] ?? '',
    'status'    => $_GET['status'] ?? '',
    'service'   => $_GET['service'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to'   => $_GET['date_to'] ?? '',
];

$filtered = admin_filter_enquiries($all_enquiries, $filters);

// Pagination
$per_page = 15;
$total_rows = count($filtered);
$total_pages = max(1, (int) ceil($total_rows / $per_page));
$current_page = max(1, min($total_pages, (int) ($_GET['page'] ?? 1)));
$offset = ($current_page - 1) * $per_page;
$paged_enquiries = array_slice($filtered, $offset, $per_page);

// Distinct services for dropdown
$available_services = [];
foreach ($all_enquiries as $e) {
    if (!empty($e['service'])) {
        $available_services[$e['service']] = true;
    }
}
ksort($available_services);

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Customer Enquiries</h1>
    <p class="page-subtitle">Showing <?= number_format($total_rows) ?> total submissions recorded from the website contact forms.</p>
  </div>
  <div style="display:flex; gap:0.75rem;">
    <a href="<?= url('admin/export.php') . '?' . http_build_query($filters) ?>" class="btn btn--secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export Filtered CSV
    </a>
  </div>
</div>

<!-- Filters Bar -->
<div class="card">
  <div class="card-body" style="padding: 1.25rem;">
    <form method="GET" action="" class="filters-bar">
      <input type="text" name="q" class="form-input" placeholder="Search reference, name, email, phone..." value="<?= htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8') ?>" style="min-width: 240px; flex: 1;">

      <select name="status" class="form-select">
        <option value="">All Statuses</option>
        <?php foreach (['New', 'Contacted', 'In Progress', 'Closed'] as $st): ?>
          <option value="<?= $st ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>

      <select name="service" class="form-select">
        <option value="">All Services</option>
        <?php foreach (array_keys($available_services) as $svc): ?>
          <option value="<?= htmlspecialchars($svc, ENT_QUOTES, 'UTF-8') ?>" <?= $filters['service'] === $svc ? 'selected' : '' ?>>
            <?= htmlspecialchars($svc, ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>

      <input type="date" name="date_from" class="form-input" title="From Date" value="<?= htmlspecialchars($filters['date_from'], ENT_QUOTES, 'UTF-8') ?>">
      <input type="date" name="date_to" class="form-input" title="To Date" value="<?= htmlspecialchars($filters['date_to'], ENT_QUOTES, 'UTF-8') ?>">

      <button type="submit" class="btn btn--brand">Filter</button>
      <?php if (array_filter($filters)): ?>
        <a href="<?= url('admin/enquiries.php') ?>" class="btn btn--outline">Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- Enquiries Table -->
<div class="card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Reference</th>
          <th>Received Date</th>
          <th>Lead Name / Contact</th>
          <th>Company</th>
          <th>Service</th>
          <th>Budget</th>
          <th>Status</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($paged_enquiries)): ?>
          <tr>
            <td colspan="8" style="text-align:center; padding: 3rem; color: var(--admin-text-muted);">
              No enquiries match your search criteria.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($paged_enquiries as $enq): ?>
            <tr>
              <td class="primary">
                <a href="<?= url('admin/enquiry.php?ref=' . urlencode($enq['reference'])) ?>" style="font-family:monospace; color:var(--admin-brand); font-weight:700;">
                  <?= htmlspecialchars($enq['reference'], ENT_QUOTES, 'UTF-8') ?>
                </a>
              </td>
              <td>
                <div style="font-weight:500; color:var(--admin-text-primary);">
                  <?= date('M d, Y', strtotime($enq['received_at'] ?? 'now')) ?>
                </div>
                <div style="font-size:11px; color:var(--admin-text-muted);">
                  <?= date('h:i A', strtotime($enq['received_at'] ?? 'now')) ?>
                </div>
              </td>
              <td>
                <div style="font-weight:600; color:var(--admin-text-primary);"><?= htmlspecialchars($enq['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div style="font-size:12px; color:var(--admin-text-muted);">
                  <a href="mailto:<?= htmlspecialchars($enq['email'], ENT_QUOTES, 'UTF-8') ?>" style="color:inherit;"><?= htmlspecialchars($enq['email'], ENT_QUOTES, 'UTF-8') ?></a> &bull; 
                  <a href="tel:<?= htmlspecialchars($enq['phone'], ENT_QUOTES, 'UTF-8') ?>" style="color:inherit;"><?= htmlspecialchars($enq['phone'], ENT_QUOTES, 'UTF-8') ?></a>
                </div>
              </td>
              <td><?= htmlspecialchars($enq['company'] ?: '—', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($enq['service'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><span style="font-size:12px;"><?= htmlspecialchars($enq['budget'] ?: 'Not specified', ENT_QUOTES, 'UTF-8') ?></span></td>
              <td><?= admin_status_badge($enq['status'] ?? 'New') ?></td>
              <td style="text-align:right;">
                <a href="<?= url('admin/enquiry.php?ref=' . urlencode($enq['reference'])) ?>" class="btn btn--outline btn--sm">
                  View &amp; Edit
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination Controls -->
  <?php if ($total_pages > 1): ?>
    <div class="card-header" style="justify-content: space-between; border-top: 1px solid var(--admin-border); border-bottom: none;">
      <div style="font-size: 13px; color: var(--admin-text-muted);">
        Showing <?= $offset + 1 ?> to <?= min($offset + $per_page, $total_rows) ?> of <?= $total_rows ?> records
      </div>
      <div style="display:flex; gap:0.35rem;">
        <?php for ($p = 1; $p <= $total_pages; $p++): 
          $page_query = array_merge($filters, ['page' => $p]);
        ?>
          <a href="?<?= http_build_query($page_query) ?>" class="btn btn--sm <?= $p === $current_page ? 'btn--brand' : 'btn--outline' ?>">
            <?= $p ?>
          </a>
        <?php endfor; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
