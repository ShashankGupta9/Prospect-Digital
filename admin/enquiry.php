<?php
/**
 * Prospect Digital — Single Enquiry Detail & Management
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$ref = trim((string) ($_GET['ref'] ?? ''));
if ($ref === '') {
    admin_set_flash('error', 'Invalid enquiry reference.');
    header('Location: ' . url('admin/enquiries.php'));
    exit;
}

$enquiry = admin_get_enquiry_by_ref($ref);
if (!$enquiry) {
    admin_set_flash('error', "Enquiry with reference {$ref} was not found.");
    header('Location: ' . url('admin/enquiries.php'));
    exit;
}

// Handle update of status / notes / priority
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf($csrf)) {
        admin_set_flash('error', 'CSRF validation failed. Please try again.');
    } else {
        $new_status   = trim((string) ($_POST['status'] ?? 'New'));
        $new_priority = trim((string) ($_POST['priority'] ?? 'Normal'));
        $new_notes    = trim((string) ($_POST['notes'] ?? ''));

        $success = admin_update_enquiry_meta($ref, [
            'status'   => $new_status,
            'priority' => $new_priority,
            'notes'    => $new_notes,
        ]);

        if ($success) {
            admin_set_flash('success', 'Enquiry details updated successfully.');
        } else {
            admin_set_flash('error', 'Could not save metadata. Check file permissions.');
        }

        // Reload fresh record
        header('Location: ' . url('admin/enquiry.php?ref=' . urlencode($ref)));
        exit;
    }
}

$page_title = 'Enquiry ' . $enquiry['reference'];
$active_nav = 'enquiries';
$csrf_token = admin_csrf_token();

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <div style="margin-bottom: 0.5rem;">
      <a href="<?= url('admin/enquiries.php') ?>" style="color: var(--admin-text-muted); font-size: 13px;">
        &#8592; Back to all enquiries
      </a>
    </div>
    <div style="display:flex; align-items:center; gap:0.75rem;">
      <h1 class="page-title" style="font-family:monospace;"><?= htmlspecialchars($enquiry['reference'], ENT_QUOTES, 'UTF-8') ?></h1>
      <?= admin_status_badge($enquiry['status'] ?? 'New') ?>
    </div>
    <p class="page-subtitle">Submitted on <?= date('F d, Y \a\t h:i:s A T', strtotime($enquiry['received_at'] ?? 'now')) ?></p>
  </div>
  <div style="display:flex; gap:0.75rem;">
    <a href="mailto:<?= htmlspecialchars($enquiry['email'], ENT_QUOTES, 'UTF-8') ?>?subject=<?= urlencode('Regarding your enquiry with Prospect Digital (' . $enquiry['reference'] . ')') ?>" class="btn btn--brand">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Reply via Email
    </a>
    <a href="https://wa.me/<?= preg_replace('/\D/', '', $enquiry['phone'] ?? '') ?>?text=<?= urlencode('Hello ' . $enquiry['name'] . ', regarding your enquiry ' . $enquiry['reference'] . ' with Prospect Digital:') ?>" target="_blank" rel="noopener" class="btn btn--secondary">
      WhatsApp Lead
    </a>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
  <!-- Left Column: Lead Information & Message -->
  <div>
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Enquiry Details</h2>
      </div>
      <div class="card-body">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
          <div>
            <div class="form-label">Client Name</div>
            <div style="font-size: 15px; font-weight: 700; color: var(--admin-text-primary);">
              <?= htmlspecialchars($enquiry['name'], ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
          <div>
            <div class="form-label">Company / Organisation</div>
            <div style="font-size: 15px; font-weight: 600; color: var(--admin-text-primary);">
              <?= htmlspecialchars($enquiry['company'] ?: 'Individual / Not specified', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
          <div>
            <div class="form-label">Email Address</div>
            <div style="font-size: 14px; font-weight: 500;">
              <a href="mailto:<?= htmlspecialchars($enquiry['email'], ENT_QUOTES, 'UTF-8') ?>" style="color: var(--admin-accent);">
                <?= htmlspecialchars($enquiry['email'], ENT_QUOTES, 'UTF-8') ?>
              </a>
            </div>
          </div>
          <div>
            <div class="form-label">Phone Number</div>
            <div style="font-size: 14px; font-weight: 500;">
              <a href="tel:<?= htmlspecialchars($enquiry['phone'], ENT_QUOTES, 'UTF-8') ?>" style="color: var(--admin-accent);">
                <?= htmlspecialchars($enquiry['phone'], ENT_QUOTES, 'UTF-8') ?>
              </a>
            </div>
          </div>
          <div>
            <div class="form-label">Service of Interest</div>
            <div style="font-size: 14px; font-weight: 600; color: var(--admin-text-primary);">
              <?= htmlspecialchars($enquiry['service'], ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
          <div>
            <div class="form-label">Budget Range</div>
            <div style="font-size: 14px; font-weight: 600; color: var(--admin-text-primary);">
              <?= htmlspecialchars($enquiry['budget'] ?: 'Not specified', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
        </div>

        <div style="border-top: 1px solid var(--admin-border); padding-top: 1.25rem;">
          <div class="form-label">Message / Project Description</div>
          <div style="background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius-sm); padding: 1.25rem; font-size: 14px; line-height: 1.6; white-space: pre-wrap; color: var(--admin-text-primary);">
            <?= htmlspecialchars($enquiry['message'], ENT_QUOTES, 'UTF-8') ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Technical & Audit Info -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Technical Audit Trail</h2>
      </div>
      <div class="card-body" style="font-size: 12px; color: var(--admin-text-secondary); line-height: 1.8;">
        <div><strong>Source Page:</strong> <?= htmlspecialchars($enquiry['source_page'] ?? 'direct', ENT_QUOTES, 'UTF-8') ?></div>
        <div><strong>Anonymized IP Hash:</strong> <code style="color:var(--admin-brand);"><?= htmlspecialchars($enquiry['ip_hash'] ?? '—', ENT_QUOTES, 'UTF-8') ?></code></div>
        <div><strong>User Agent:</strong> <?= htmlspecialchars($enquiry['user_agent'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
        <div><strong>Mail Dispatch Attempt:</strong> <?= !empty($enquiry['mail_sent']) ? 'Dispatched' : 'Recorded in local data store (No mail MTA)' ?></div>
      </div>
    </div>
  </div>

  <!-- Right Column: Lead Status & Internal Notes -->
  <div>
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Lead Management</h2>
      </div>
      <div class="card-body">
        <form method="POST" action="">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

          <div class="form-group">
            <label class="form-label" for="status">Lead Status</label>
            <select name="status" id="status" class="form-select">
              <?php foreach (['New', 'Contacted', 'In Progress', 'Closed'] as $opt): ?>
                <option value="<?= $opt ?>" <?= ($enquiry['status'] ?? 'New') === $opt ? 'selected' : '' ?>>
                  <?= $opt ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="priority">Priority</label>
            <select name="priority" id="priority" class="form-select">
              <?php foreach (['Normal', 'High', 'Low'] as $p): ?>
                <option value="<?= $p ?>" <?= ($enquiry['priority'] ?? 'Normal') === $p ? 'selected' : '' ?>>
                  <?= $p ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="notes">Internal Staff Notes</label>
            <textarea name="notes" id="notes" class="form-textarea" rows="6" placeholder="Add follow-up notes, call logs, meeting dates, or quote amounts..."><?= htmlspecialchars($enquiry['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <div style="font-size: 11px; color: var(--admin-text-muted); margin-top: 0.35rem;">
              Notes are private and saved into isolated metadata. Original customer submissions are never altered.
            </div>
          </div>

          <button type="submit" class="btn btn--brand" style="width: 100%;">
            Save Updates
          </button>
        </form>
      </div>
    </div>

    <!-- Status History -->
    <?php if (!empty($enquiry['status_history'])): ?>
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Status Change History</h2>
        </div>
        <div class="card-body" style="font-size: 12px;">
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem;">
            <?php foreach (array_reverse($enquiry['status_history']) as $hist): ?>
              <li style="border-left: 2px solid var(--admin-brand); padding-left: 0.75rem;">
                <div>Changed to <strong><?= htmlspecialchars($hist['to'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></div>
                <div style="color: var(--admin-text-muted); font-size: 11px;">
                  <?= date('M d, Y h:i A', strtotime($hist['changed_at'] ?? 'now')) ?> by <?= htmlspecialchars($hist['by'] ?? 'admin', ENT_QUOTES, 'UTF-8') ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
