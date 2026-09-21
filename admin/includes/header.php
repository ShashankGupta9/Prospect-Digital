<?php
/**
 * Prospect Digital — Admin Header & Navigation Component
 */

declare(strict_types=1);

if (!defined('ADMIN_PANEL_ACTIVE')) {
    exit('Direct access not permitted.');
}

$active_nav = $active_nav ?? 'dashboard';
$page_title = $page_title ?? 'Admin Console';
$flash = admin_get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?> — Prospect Digital Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= url('admin/assets/css/admin.css') ?>?v=<?= filemtime(__DIR__ . '/../assets/css/admin.css') ?>">
</head>
<body class="admin-body">

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="admin-sidebar__brand">
    <div class="admin-sidebar__logo">P</div>
    <span class="admin-sidebar__title">Prospect</span>
    <span class="admin-sidebar__badge">Console</span>
  </div>

  <nav class="admin-nav">
    <div class="admin-nav__label">Overview</div>
    <a href="<?= url('admin/index.php') ?>" class="admin-nav__item <?= $active_nav === 'dashboard' ? 'active' : '' ?>">
      <svg class="admin-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <div class="admin-nav__label">CRM &amp; Inquiries</div>
    <a href="<?= url('admin/enquiries.php') ?>" class="admin-nav__item <?= $active_nav === 'enquiries' ? 'active' : '' ?>">
      <svg class="admin-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Enquiries
    </a>
    <a href="<?= url('admin/export.php') ?>" class="admin-nav__item <?= $active_nav === 'export' ? 'active' : '' ?>">
      <svg class="admin-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export Data (CSV)
    </a>

    <div class="admin-nav__label">Content &amp; Assets</div>
    <a href="<?= url('admin/store.php') ?>" class="admin-nav__item <?= $active_nav === 'store' ? 'active' : '' ?>">
      <svg class="admin-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      Store &amp; Products
    </a>
    <a href="<?= url('admin/orders.php') ?>" class="admin-nav__item <?= $active_nav === 'orders' ? 'active' : '' ?>">
      <svg class="admin-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      Customer Orders
    </a>
    <a href="<?= url('admin/media.php') ?>" class="admin-nav__item <?= $active_nav === 'media' ? 'active' : '' ?>">
      <svg class="admin-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      Media &amp; Images
    </a>

    <div class="admin-nav__label">System</div>
    <a href="<?= url('admin/settings.php') ?>" class="admin-nav__item <?= $active_nav === 'settings' ? 'active' : '' ?>">
      <svg class="admin-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Settings &amp; Diagnostics
    </a>
    <a href="<?= url('/') ?>" target="_blank" class="admin-nav__item">
      <svg class="admin-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      View Website &#8599;
    </a>
  </nav>

  <div class="admin-sidebar__footer">
    <div class="admin-user-info">
      <span class="admin-user-name"><?= htmlspecialchars($_SESSION['admin_user']['name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></span>
      <span class="admin-user-role"><?= htmlspecialchars($_SESSION['admin_user']['role'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <a href="<?= url('admin/logout.php') ?>" title="Sign Out" style="color: var(--admin-text-muted); display:flex; align-items:center;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </a>
  </div>
</aside>

<!-- Main Wrapper -->
<div class="admin-main">
  <!-- Topbar -->
  <header class="admin-topbar">
    <div class="admin-topbar__left">
      <button class="admin-toggle-btn" id="adminSidebarToggle" aria-label="Toggle Navigation">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div class="admin-breadcrumb">
        <span>Prospect Console</span> / <?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>
    <div class="admin-topbar__right">
      <a href="<?= url('admin/logout.php') ?>" class="btn btn--outline btn--sm">
        Sign Out
      </a>
    </div>
  </header>

  <!-- Page Content Container -->
  <main class="admin-content">
    <?php if ($flash): ?>
      <div class="alert alert--<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?> alert--auto-dismiss">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>
