<?php
/**
 * Prospect Digital — Shopping Cart Page
 * ---------------------------------------------------------------------------
 * Complete cart management:
 *   • Supports Add, Update Quantity, Remove Item, and Clear Cart actions
 *   • Supports both AJAX requests (JSON response) and standard POST redirects
 *   • Detailed summary calculations: subtotal, 18% GST, shipping, discount, total
 *   • Clean empty-cart state with return to store actions
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/store-functions.php';

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action     = clean_text($_POST['action'] ?? '', 40);
    $product_id = clean_text($_POST['product_id'] ?? '', 80);
    $quantity   = (int) ($_POST['quantity'] ?? 1);

    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

    if ($action === 'add' && $product_id !== '') {
        store_cart_add($product_id, $quantity);
    } elseif ($action === 'update' && $product_id !== '') {
        store_cart_update($product_id, $quantity);
    } elseif ($action === 'remove' && $product_id !== '') {
        store_cart_remove($product_id);
    } elseif ($action === 'clear') {
        store_cart_clear();
    } elseif ($action === 'apply_promo') {
        $promo = strtoupper(clean_text($_POST['promo_code'] ?? '', 30));
        if ($promo === 'PROSPECT10' || $promo === 'WELCOME10') {
            $raw_cart = store_cart_get();
            $_SESSION['pd_cart_discount'] = round($raw_cart['subtotal'] * 0.10, 2);
            $_SESSION['pd_cart_promo']    = $promo;
        } else {
            $_SESSION['pd_promo_error'] = 'Invalid or expired promotional code.';
        }
    }

    if ($is_ajax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'    => true,
            'cart'  => store_cart_get(),
            'count' => store_cart_count(),
        ]);
        exit;
    }

    header('Location: ' . url('cart'));
    exit;
}

$cart = store_cart_get();

$promo_error = $_SESSION['pd_promo_error'] ?? null;
unset($_SESSION['pd_promo_error']);

$page_title       = 'Your Shopping Cart — Prospect Digital Store';
$page_description = 'Review your selected hardware, software licenses, and developer tools before proceeding to secure checkout.';
$body_class       = 'page-store-cart';

$breadcrumbs = [
    ['name' => 'Store', 'url' => 'store'],
    ['name' => 'Cart',  'url' => 'cart'],
];

require __DIR__ . '/includes/header.php';
?>

<div class="store-ambient-wrap">
  <div class="contact-tech-grid" aria-hidden="true"></div>

  <section class="section store-cart-section">
    <div class="container">
      <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>

      <div class="cart-header-row" data-reveal>
        <div>
          <h1 class="cart-page-title">Shopping Cart</h1>
          <p class="cart-page-sub">
            <?= $cart['is_empty'] ? '0 items in your cart' : $cart['item_count'] . ' item' . ($cart['item_count'] === 1 ? '' : 's') . ' selected for purchase' ?>
          </p>
        </div>
        <?php if (!$cart['is_empty']): ?>
          <form action="<?= e(url('cart')) ?>" method="post">
            <input type="hidden" name="action" value="clear">
            <button type="submit" class="cart-clear-btn" onclick="return confirm('Clear all items from your cart?');">
              <?= icon('trash', 'icon') ?> Clear Cart
            </button>
          </form>
        <?php endif; ?>
      </div>

      <?php if ($cart['is_empty']): ?>
        <!-- ==================== EMPTY CART STATE ==================== -->
        <div class="store-empty-card cart-empty-card" data-reveal>
          <div class="store-empty-icon-wrap" style="margin:0 auto 1.5rem;">
            <?= icon('shopping-bag', 'icon store-empty-icon') ?>
          </div>

          <h2 class="store-empty-title">Your shopping cart is empty</h2>
          <p class="store-empty-text">
            You have not added any hardware devices, software licenses, or developer packages to your cart yet.
          </p>

          <div class="cart-empty-actions">
            <a href="<?= e(url('store')) ?>" class="btn btn--brand btn--md">
              <?= icon('arrow-left', 'icon btn__icon') ?>
              <span>Explore Store Catalog</span>
            </a>
            <a href="<?= e(url('wishlist')) ?>" class="btn btn--outline btn--md">
              <?= icon('heart', 'icon btn__icon') ?>
              <span>View Wishlist</span>
            </a>
          </div>
        </div>

      <?php else: ?>
        <!-- ==================== CART ITEMS & SUMMARY ==================== -->
        <div class="cart-layout-grid">
          <!-- Left: Items List -->
          <div class="cart-items-panel">
            <div class="cart-items-table-header">
              <span class="col-item">Product</span>
              <span class="col-price">Unit Price</span>
              <span class="col-qty">Quantity</span>
              <span class="col-total">Total</span>
              <span class="col-action"><span class="sr-only">Actions</span></span>
            </div>

            <div class="cart-items-list">
              <?php foreach ($cart['items'] as $row):
                  $p = $row['product'];
                  $p_id = $p['id'];
                  $thumb = !empty($p['images'][0]['src']) ? $p['images'][0]['src'] : null;
              ?>
                <div class="cart-item-row" data-product-id="<?= e($p_id) ?>">
                  <div class="col-item cart-item-info">
                    <div class="cart-item-thumb">
                      <?php if ($thumb): ?>
                        <img src="<?= e(asset($thumb)) ?>" alt="<?= e($p['name']) ?>">
                      <?php else: ?>
                        <div class="cart-item-fallback-icon"><?= icon('box', 'icon') ?></div>
                      <?php endif; ?>
                    </div>
                    <div>
                      <h3 class="cart-item-name">
                        <a href="<?= e(url('store/' . $p['slug'])) ?>"><?= e($p['name']) ?></a>
                      </h3>
                      <span class="cart-item-sku">SKU: <?= e($p['sku']) ?></span>
                    </div>
                  </div>

                  <div class="col-price cart-item-price">
                    <?= e(store_format_currency($row['unit_price'])) ?>
                  </div>

                  <div class="col-qty cart-item-qty">
                    <form action="<?= e(url('cart')) ?>" method="post" class="cart-qty-form" data-cart-update-form>
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="product_id" value="<?= e($p_id) ?>">
                      <div class="product-qty-stepper product-qty-stepper--compact">
                        <button type="submit" name="quantity" value="<?= max(0, $row['quantity'] - 1) ?>" class="product-qty-btn" aria-label="Decrease quantity">
                          <?= icon('minus', 'icon') ?>
                        </button>
                        <span class="cart-qty-val"><?= $row['quantity'] ?></span>
                        <button type="submit" name="quantity" value="<?= $row['quantity'] + 1 ?>" class="product-qty-btn" aria-label="Increase quantity">
                          <?= icon('plus', 'icon') ?>
                        </button>
                      </div>
                    </form>
                  </div>

                  <div class="col-total cart-item-total">
                    <?= e(store_format_currency($row['line_total'])) ?>
                  </div>

                  <div class="col-action cart-item-remove">
                    <form action="<?= e(url('cart')) ?>" method="post">
                      <input type="hidden" name="action" value="remove">
                      <input type="hidden" name="product_id" value="<?= e($p_id) ?>">
                      <button type="submit" class="cart-remove-btn" title="Remove item" aria-label="Remove <?= e($p['name']) ?>">
                        <?= icon('close', 'icon') ?>
                      </button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="cart-footer-links">
              <a href="<?= e(url('store')) ?>" class="btn btn--outline btn--sm">
                <?= icon('arrow-left', 'icon btn__icon') ?>
                <span>Continue Shopping</span>
              </a>
            </div>
          </div>

          <!-- Right: Order Summary Sidebar -->
          <aside class="cart-summary-sidebar">
            <div class="cart-summary-card">
              <h2 class="cart-summary-title">Order Summary</h2>

              <div class="cart-summary-lines">
                <div class="cart-summary-line">
                  <span>Subtotal (<?= $cart['item_count'] ?> items)</span>
                  <span><?= e(store_format_currency($cart['subtotal'])) ?></span>
                </div>

                <div class="cart-summary-line">
                  <span>Estimated GST (18%)</span>
                  <span><?= e(store_format_currency($cart['tax_amount'])) ?></span>
                </div>

                <div class="cart-summary-line">
                  <span>Standard Shipping</span>
                  <span><?= $cart['shipping'] == 0 ? '<strong style="color:#059669;">FREE</strong>' : e(store_format_currency($cart['shipping'])) ?></span>
                </div>

                <?php if ($cart['discount'] > 0): ?>
                  <div class="cart-summary-line cart-summary-line--discount">
                    <span>Discount (<?= e($cart['promo_code']) ?>)</span>
                    <span>- <?= e(store_format_currency($cart['discount'])) ?></span>
                  </div>
                <?php endif; ?>

                <div class="cart-summary-divider"></div>

                <div class="cart-summary-line cart-summary-line--total">
                  <span>Estimated Total</span>
                  <span class="cart-grand-total"><?= e(store_format_currency($cart['grand_total'])) ?></span>
                </div>
              </div>

              <!-- Promo Code Box -->
              <form action="<?= e(url('cart')) ?>" method="post" class="cart-promo-form">
                <input type="hidden" name="action" value="apply_promo">
                <div class="cart-promo-input-group">
                  <input type="text"
                         name="promo_code"
                         class="cart-promo-input"
                         placeholder="Promo or coupon code…"
                         value="<?= e($cart['promo_code']) ?>"
                         aria-label="Enter promotional code">
                  <button type="submit" class="btn btn--outline btn--sm cart-promo-btn">
                    Apply
                  </button>
                </div>
                <?php if ($promo_error): ?>
                  <p class="cart-promo-error"><?= e($promo_error) ?></p>
                <?php elseif ($cart['discount'] > 0): ?>
                  <p class="cart-promo-success">✓ Promo code <strong><?= e($cart['promo_code']) ?></strong> applied!</p>
                <?php endif; ?>
              </form>

              <!-- Checkout Button -->
              <a href="<?= e(url('checkout')) ?>" class="btn btn--brand btn--lg cart-checkout-btn">
                <span>Proceed to Checkout</span>
                <?= icon('arrow', 'icon btn__icon') ?>
              </a>

              <!-- Trust & Security Badges -->
              <div class="cart-trust-badges">
                <div class="cart-trust-badge">
                  <?= icon('shield', 'icon') ?>
                  <span>256-Bit SSL Checkout</span>
                </div>
                <div class="cart-trust-badge">
                  <?= icon('check', 'icon') ?>
                  <span>1-Year Official Warranty</span>
                </div>
              </div>

            </div>
          </aside>
        </div>
      <?php endif; ?>

    </div>
  </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
