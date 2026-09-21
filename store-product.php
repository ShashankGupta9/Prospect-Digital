<?php
/**
 * Prospect Digital — Store Product Detail Page
 * ---------------------------------------------------------------------------
 * Complete product detail layout:
 *   • Interactive image gallery with thumbnails
 *   • SKU, stock status, pricing with discount badges
 *   • Quantity controls, Add to Cart, and Wishlist actions
 *   • Detailed tabs: Description, Specifications table, Features checklist, Warranty
 *   • Schema.org Product structured data
 *   • Graceful not-found state when slug is absent or invalid
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/store-functions.php';

$slug    = clean_text($_GET['slug'] ?? '', 120);
$product = store_get_product_by_slug($slug);

if (!$product) {
    // 404 / Product Not Found
    http_response_code(404);
    $page_title       = 'Product Not Found — Prospect Digital Store';
    $page_description = 'The requested store item could not be found or has not been listed yet.';
    $body_class       = 'page-store-notfound';

    $breadcrumbs = [
        ['name' => 'Store', 'url' => 'store'],
        ['name' => 'Not Found', 'url' => ''],
    ];

    require __DIR__ . '/includes/header.php';
    ?>
    <section class="section">
      <div class="container container--narrow">
        <div class="store-empty-card" style="text-align:center;padding:3rem 1.5rem;" data-reveal>
          <div class="store-empty-icon-wrap" style="margin:0 auto 1.5rem;">
            <?= icon('box', 'icon store-empty-icon') ?>
          </div>
          <h1 style="font-size:var(--fs-h2);margin-bottom:0.75rem;">Product Not Found</h1>
          <p class="muted" style="max-width:480px;margin:0 auto 1.5rem;line-height:1.6;">
            We could not find an active product with the identifier <code><?= e($slug ?: 'unknown') ?></code>. It may have been relocated or is currently in pre-production.
          </p>
          <div style="display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;">
            <a href="<?= e(url('store')) ?>" class="btn btn--brand btn--md">
              <?= icon('arrow-left', 'icon btn__icon') ?>
              <span>Return to Store</span>
            </a>
            <a href="<?= e(url('contact')) ?>" class="btn btn--outline btn--md">
              <span>Contact Engineering</span>
            </a>
          </div>
        </div>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Product Found: extract details
$prod_id         = $product['id'];
$prod_name       = $product['name'];
$prod_sku        = $product['sku'];
$prod_category   = $product['category'];
$prod_cat_slug   = $product['category_slug'];
$prod_price      = $product['price'];
$prod_discount   = $product['discount_price'];
$prod_stock      = $product['stock_status'];
$prod_stock_qty  = $product['stock_quantity'];
$prod_short_desc = $product['short_description'];
$prod_full_desc  = $product['full_description'];
$prod_specs      = $product['specifications'];
$prod_features   = $product['features'];
$prod_images     = $product['images'];

$is_in_stock     = ($prod_stock === 'in_stock');
$is_out_of_stock = ($prod_stock === 'out_of_stock');
$is_preorder     = ($prod_stock === 'preorder');

$effective_price = $prod_discount !== null ? $prod_discount : $prod_price;
$discount_pct    = $prod_discount !== null ? store_calc_discount_percent($prod_price, $prod_discount) : 0;
$is_wishlisted   = store_wishlist_has($prod_id);

$page_title       = $product['seo_title'] ?: ($prod_name . ' — Official Store | Prospect Digital');
$page_description = $product['seo_description'] ?: $prod_short_desc;
$body_class       = 'page-store-detail';

$breadcrumbs = [
    ['name' => 'Store', 'url' => 'store'],
    ['name' => $prod_category, 'url' => 'store?category=' . urlencode($prod_cat_slug)],
    ['name' => $prod_name, 'url' => 'store/' . $slug],
];

// Schema.org Product JSON-LD
$page_jsonld = [[
    '@context'    => 'https://schema.org',
    '@type'       => 'Product',
    'name'        => $prod_name,
    'sku'         => $prod_sku,
    'description' => $prod_short_desc,
    'brand'       => [
        '@type' => 'Brand',
        'name'  => COMPANY_NAME,
    ],
    'offers'      => [
        '@type'         => 'Offer',
        'priceCurrency' => 'INR',
        'price'         => number_format($effective_price, 2, '.', ''),
        'availability'  => $is_in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'url'           => absolute_url('store/' . $slug),
        'seller'        => [
            '@type' => 'Organization',
            'name'  => COMPANY_NAME,
        ],
    ],
]];

require __DIR__ . '/includes/header.php';
?>

<div class="store-ambient-wrap">
  <div class="contact-tech-grid" aria-hidden="true"></div>

  <div class="container" style="padding-top:1.5rem;position:relative;z-index:2;">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>

    <div class="product-detail-layout">
      <!-- ==================== LEFT: IMAGE GALLERY ==================== -->
      <div class="product-gallery-panel">
        <div class="product-gallery-main">
          <?php if (!empty($prod_images)): ?>
            <img id="productMainImage"
                 src="<?= e(asset($prod_images[0]['src'])) ?>"
                 alt="<?= e($prod_images[0]['alt'] ?? $prod_name) ?>"
                 class="product-gallery-img"
                 loading="eager">
          <?php else: ?>
            <div class="product-gallery-fallback">
              <div class="product-gallery-fallback-icon"><?= icon('box', 'icon') ?></div>
              <span class="product-gallery-fallback-text"><?= e($prod_sku) ?></span>
            </div>
          <?php endif; ?>

          <?php if ($discount_pct > 0): ?>
            <span class="store-badge store-badge--sale product-gallery-sale-badge"><?= $discount_pct ?>% OFF</span>
          <?php endif; ?>
        </div>

        <!-- Thumbnails Strip -->
        <?php if (count($prod_images) > 1): ?>
          <div class="product-thumbnails-strip" role="group" aria-label="Product thumbnails">
            <?php foreach ($prod_images as $idx => $img): ?>
              <button type="button"
                      class="product-thumb-btn<?= $idx === 0 ? ' is-active' : '' ?>"
                      data-full-src="<?= e(asset($img['src'])) ?>"
                      data-alt="<?= e($img['alt'] ?? $prod_name) ?>"
                      aria-label="View image <?= $idx + 1 ?>">
                <img src="<?= e(asset($img['src'])) ?>" alt="<?= e($img['alt'] ?? '') ?>" loading="lazy">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- ==================== RIGHT: PRODUCT INFORMATION ==================== -->
      <div class="product-info-panel">
        <div class="product-info-meta">
          <a href="<?= e(url('store?category=' . urlencode($prod_cat_slug))) ?>" class="product-cat-link">
            <?= e($prod_category) ?>
          </a>
          <span class="product-sku-tag">SKU: <?= e($prod_sku) ?></span>
        </div>

        <h1 class="product-title"><?= e($prod_name) ?></h1>

        <!-- Stock Availability Pill -->
        <div class="product-stock-wrap">
          <?php if ($is_in_stock): ?>
            <span class="product-stock-pill is-instock">
              <span class="pulse-dot"></span> In Stock (<?= $prod_stock_qty ?> units available)
            </span>
          <?php elseif ($is_preorder): ?>
            <span class="product-stock-pill is-preorder">
              <span class="pulse-dot" style="background:#d97706;"></span> Available for Pre-Order
            </span>
          <?php else: ?>
            <span class="product-stock-pill is-outstock">
              Sold Out / Out of Stock
            </span>
          <?php endif; ?>
        </div>

        <!-- Pricing Section -->
        <div class="product-price-section">
          <span class="product-price-current"><?= e(store_format_currency($effective_price)) ?></span>
          <?php if ($prod_discount !== null): ?>
            <span class="product-price-old"><?= e(store_format_currency($prod_price)) ?></span>
            <span class="product-save-pill">Save <?= e(store_format_currency($prod_price - $prod_discount)) ?></span>
          <?php endif; ?>
          <span class="product-tax-note">+ 18% GST calculated at checkout</span>
        </div>

        <?php if ($prod_short_desc !== ''): ?>
          <p class="product-short-desc"><?= e($prod_short_desc) ?></p>
        <?php endif; ?>

        <!-- Add to Cart & Wishlist Actions -->
        <form action="<?= e(url('cart')) ?>" method="post" class="product-actions-form">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= e($prod_id) ?>">

          <div class="product-qty-row">
            <div class="product-qty-stepper">
              <label for="productQtyInput" class="sr-only">Quantity</label>
              <button type="button" class="product-qty-btn" data-qty-action="decrease" aria-label="Decrease quantity">
                <?= icon('minus', 'icon') ?>
              </button>
              <input type="number"
                     id="productQtyInput"
                     name="quantity"
                     class="product-qty-input"
                     value="1"
                     min="1"
                     max="<?= $is_in_stock ? max(1, $prod_stock_qty) : 10 ?>"
                     aria-label="Selected quantity">
              <button type="button" class="product-qty-btn" data-qty-action="increase" aria-label="Increase quantity">
                <?= icon('plus', 'icon') ?>
              </button>
            </div>

            <button type="submit"
                    class="btn btn--brand btn--lg product-add-cart-btn"
                    <?= $is_out_of_stock ? 'disabled' : '' ?>>
              <?= icon('cart', 'icon btn__icon') ?>
              <span><?= $is_out_of_stock ? 'Out of Stock' : ($is_preorder ? 'Pre-Order Now' : 'Add to Cart') ?></span>
            </button>

            <button type="button"
                    class="btn btn--outline btn--lg product-wishlist-toggle-btn<?= $is_wishlisted ? ' is-active' : '' ?>"
                    data-product-id="<?= e($prod_id) ?>"
                    aria-label="<?= $is_wishlisted ? 'Remove from wishlist' : 'Save to wishlist' ?>"
                    title="Save to wishlist">
              <?= icon('heart', 'icon') ?>
            </button>
          </div>
        </form>

        <!-- Quick Value Highlights -->
        <div class="product-perks-list">
          <div class="product-perk-item">
            <?= icon('truck', 'icon product-perk-icon') ?>
            <span>Pan-India insured courier delivery (2–5 working days)</span>
          </div>
          <div class="product-perk-item">
            <?= icon('shield', 'icon product-perk-icon') ?>
            <span>1-Year Official Prospect Digital Replacement Warranty</span>
          </div>
          <div class="product-perk-item">
            <?= icon('phone', 'icon product-perk-icon') ?>
            <span>Direct Bhopal tech team support via WhatsApp &amp; Phone</span>
          </div>
        </div>

      </div>
    </div>

    <!-- ==================== TABS: DESCRIPTION, SPECS, FEATURES ==================== -->
    <div class="product-tabs-section" data-reveal>
      <div class="product-tabs-header" role="tablist">
        <button type="button" class="product-tab-btn is-active" data-tab-target="#tabDesc" role="tab" aria-selected="true">
          Description
        </button>
        <?php if (!empty($prod_specs)): ?>
          <button type="button" class="product-tab-btn" data-tab-target="#tabSpecs" role="tab" aria-selected="false">
            Specifications
          </button>
        <?php endif; ?>
        <?php if (!empty($prod_features)): ?>
          <button type="button" class="product-tab-btn" data-tab-target="#tabFeatures" role="tab" aria-selected="false">
            Key Features
          </button>
        <?php endif; ?>
        <button type="button" class="product-tab-btn" data-tab-target="#tabWarranty" role="tab" aria-selected="false">
          Warranty &amp; Shipping
        </button>
      </div>

      <div class="product-tabs-content">
        <!-- Description Tab -->
        <div class="product-tab-pane is-active" id="tabDesc" role="tabpanel">
          <div class="prose">
            <?= nl2br(e($prod_full_desc ?: $prod_short_desc)) ?>
          </div>
        </div>

        <!-- Specifications Tab -->
        <?php if (!empty($prod_specs)): ?>
          <div class="product-tab-pane" id="tabSpecs" role="tabpanel" hidden>
            <table class="product-specs-table">
              <tbody>
                <?php foreach ($prod_specs as $spec_key => $spec_val): ?>
                  <tr>
                    <th><?= e($spec_key) ?></th>
                    <td><?= e((string) $spec_val) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

        <!-- Key Features Tab -->
        <?php if (!empty($prod_features)): ?>
          <div class="product-tab-pane" id="tabFeatures" role="tabpanel" hidden>
            <ul class="product-features-list">
              <?php foreach ($prod_features as $feat): ?>
                <li>
                  <?= icon('check', 'icon product-feature-check') ?>
                  <span><?= e((string) $feat) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Warranty & Shipping Tab -->
        <div class="product-tab-pane" id="tabWarranty" role="tabpanel" hidden>
          <div class="prose">
            <h3>Warranty Coverage</h3>
            <p>Every hardware purchase includes our 1-Year Comprehensive Manufacturer Warranty. If a unit experiences component or firmware defects during normal operation, our engineering team in Bhopal will diagnose and replace the affected modules at zero charge.</p>

            <h3>Dispatch &amp; Delivery</h3>
            <p>Orders confirmed by 2:00 PM IST are packed and handed to our insured courier partners on the same day. Tracking numbers are transmitted via email and WhatsApp. Typical transit times are 2–3 business days for tier-1 metropolitan hubs, and 3–5 days across regional locations.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Back to Store Action -->
    <div style="margin-top:2.5rem;padding-bottom:3rem;">
      <a href="<?= e(url('store')) ?>" class="btn btn--outline btn--sm">
        <?= icon('arrow-left', 'icon btn__icon') ?>
        <span>Return to Store Catalog</span>
      </a>
    </div>

  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
