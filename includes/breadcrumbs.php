<?php
/**
 * Prospect Digital — breadcrumb trail
 * ---------------------------------------------------------------------------
 * Usage:  $crumbs = [['name' => 'Services', 'url' => 'services.php'], ...];
 *         require __DIR__ . '/../includes/breadcrumbs.php';
 * The home crumb is added automatically.
 * ---------------------------------------------------------------------------
 */
$crumbs = $crumbs ?? [];
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <ol class="breadcrumbs__list">
    <li class="breadcrumbs__item">
      <a href="<?= e(url('')) ?>"><img src=""><</a>
    </li>
    <?php $last = count($crumbs) - 1; ?>
    <?php foreach ($crumbs as $index => $crumb): ?>
      <li class="breadcrumbs__item"<?= $index === $last ? ' aria-current="page"' : '' ?>>
        <?php if (!empty($crumb['url']) && $index !== $last): ?>
          <a href="<?= e(url($crumb['url'])) ?>"><?= e($crumb['name']) ?></a>
        <?php else: ?>
          <span><?= e($crumb['name']) ?></span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
</nav>
