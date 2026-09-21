<?php
/**
 * Prospect Digital — Wishlist Page
 * ---------------------------------------------------------------------------
 * Allows customers to save items for future reference:
 *   • Supports AJAX toggling and standard form submissions
 *   • Provides "Move to Cart" and "Remove" operations
 *   • Displays professional empty wishlist state
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/store-functions.php';

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action     = clean_text($_POST['action'] ?? '', 40);
    $product_id = clean_text($_POST['product_id'] ?? '', 80);

    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

    $state = false;
    if ($action === 'toggle' && $product_id !== '') {
        $state = store_wishlist_toggle($product_id);
    } elseif ($action === 'remove' && $product_id !== '') {
        store_wishlist_toggle($product_id);
    } elseif ($action === 'move_to_cart' && $product_id !== '') {
        store_cart_add($product_id, 1);
        store_wishlist_toggle($product_id);
        if (!$is_ajax) {
            header('Location: ' . url('cart'));
            exit;
        }
    }

    if ($is_ajax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'        => true,
            'state'     => $state,
            'wishlist'  => store_wishlist_get(),
            'cart_count'=> store_cart_count(),
        ]);
        exit;
    }

    header('Location: ' . url('wishlist'));
    exit;
}

$wishlist_ids = store_wishlist_get();
$items = [];
foreach ($wishlist_ids as $id) {
    $p = store_get_product_by_id($id);
    if ($p) {
        $items[] = $p;
    }
}

$page_title       = 'Saved Wishlist Items — Prospect Digital Store';
$page_description = 'View products, hardware packages, and licenses saved in your wishlist.';
$body_class       = 'page-store-wishlist';

$breadcrumbs = [
    ['name' => 'Store',    'url' => 'store'],
    ['name' => 'Wishlist', 'url' => 'wishlist'],
];

require __DIR__ . '/includes/header.php';
?>

<div class="store-ambient-wrap">
  <div class="contact-tech-grid" aria-hidden="true"></div>

  <section class="section">
    <div class="container">
      <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>

      <div class="cart-header-row" data-reveal>
        <div>
          <h1 class="cart-page-title">Saved Wishlist</h1>
          <p class="cart-page-sub">
            <?= empty($items) ? '0 saved items' : count($items) . ' item' . (count($items) === 1 ? '' : 's') . ' in your wishlist' ?>
          </p>
        </div>
      </div>

      <?php if (empty($items)): ?>
        <!-- ==================== EMPTY WISHLIST STATE ==================== -->
        <div class="store-empty-card" style="text-align:center;padding:3rem 1.5rem;" data-reveal>
          <div class="store-empty-icon-wrap" style="margin:0 auto 1.5rem;">
            <?= icon('heart', 'icon store-empty-icon') ?>
          </div>

          <h2 class="store-empty-title">Your wishlist is empty</h2>
          <p class="store-empty-text">
            You haven't saved any hardware or software licenses to your wishlist yet.
          </p>

          <div style="display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;margin-top:1.5rem;">
            <a href="<?= e(url('store')) ?>" class="btn btn--brand btn--md">
              <?= icon('arrow-left', 'icon btn__icon') ?>
              <span>Explore Store</span>
            </a>
            <a href="<?= e(url('cart')) ?>" class="btn btn--outline btn--md">
              <?= icon('shopping-bag', 'icon btn__icon') ?>
              <span>View Cart</span>
            </a>
          </div>
        </div>

      <?php else: ?>
        <div class="store-grid">
          <?php foreach ($items as $product): ?>
            <?php require __DIR__ . '/includes/store-product-card.php'; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
