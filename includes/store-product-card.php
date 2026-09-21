<?php
/**
 * Prospect Digital — Store Product Card
 * Reusable component for displaying a product in grid layouts.
 * Expects $product array to be available in scope.
 */

$p_url = url('store/' . $product['slug']);
$p_img = !empty($product['images'][0]) ? asset($product['images'][0]) : null;
$p_cat = $product['cat_name_rel'] ?? $product['category_name'] ?? 'Uncategorized';

// Determine if there is a discount price
if (!empty($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price']) {
    $p_price = store_format_currency((float) $product['discount_price']);
    $p_orig_price = store_format_currency((float) $product['price']);
} else {
    $p_price = store_format_currency((float) $product['price']);
    $p_orig_price = null;
}
?>
<article class="store-card">
  <div class="store-card__media">
    <?php if ($p_img): ?>
      <a href="<?= e($p_url) ?>" tabindex="-1">
        <img src="<?= e($p_img) ?>" alt="<?= e($product['name']) ?>" class="store-card__img" loading="lazy">
      </a>
    <?php else: ?>
      <a href="<?= e($p_url) ?>" class="store-item-fallback-icon" tabindex="-1" style="color:var(--ink-muted); opacity: 0.3; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
      </a>
    <?php endif; ?>

    <div class="store-card__badges">
      <?php if (!empty($product['is_featured'])): ?>
        <span class="store-card__pill" style="margin-bottom:4px;">Featured</span>
      <?php endif; ?>
      <?php if (!empty($product['is_new'])): ?>
        <span class="store-card__pill" style="background:#10b981; color:#fff;">New</span>
      <?php endif; ?>
    </div>
  </div>

  <div class="store-card__body">
    <div class="store-card__cat"><?= e($p_cat) ?></div>
    <h3 class="store-card__title">
      <a href="<?= e($p_url) ?>"><?= e($product['name']) ?></a>
    </h3>
    
    <div class="store-card__desc">
      <?= e($product['short_description'] ?? '') ?>
    </div>
    
    <div class="store-card__price-row">
      <span class="store-card__price"><?= e($p_price) ?></span>
      <?php if ($p_orig_price): ?>
        <span class="store-card__orig-price"><?= e($p_orig_price) ?></span>
      <?php endif; ?>
    </div>
    
    <div class="store-card__footer">
      <a href="<?= e($p_url) ?>" class="btn btn--brand btn--sm" style="flex:1; justify-content:center;">View Details</a>
    </div>
  </div>
</article>
