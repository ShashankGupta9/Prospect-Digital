<?php
/**
 * Prospect Digital — Order Confirmation & Receipt Page
 * ---------------------------------------------------------------------------
 * Displays verified order receipt:
 *   • Order Reference number with 1-click copy
 *   • Delivery timeline & shipping address verification
 *   • Itemized invoice table with taxes & totals
 *   • Print invoice / save receipt functionality
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/store-functions.php';

$order_id = clean_text($_GET['order_id'] ?? '', 80);
$order    = store_get_order($order_id);

if (!$order) {
    http_response_code(404);
    $page_title       = 'Order Not Found — Prospect Digital Store';
    $page_description = 'We could not locate this order reference.';
    $body_class       = 'page-order-notfound';

    require __DIR__ . '/includes/header.php';
    ?>
    <section class="section">
      <div class="container container--narrow">
        <div class="store-empty-card" style="text-align:center;padding:3rem 1.5rem;" data-reveal>
          <div class="store-empty-icon-wrap" style="margin:0 auto 1.5rem;">
            <?= icon('close', 'icon store-empty-icon') ?>
          </div>
          <h1 style="font-size:var(--fs-h2);margin-bottom:0.75rem;">Order Not Found</h1>
          <p class="muted" style="max-width:480px;margin:0 auto 1.5rem;line-height:1.6;">
            We could not find an active order with tracking reference <code><?= e($order_id ?: 'unspecified') ?></code>.
          </p>
          <a href="<?= e(url('store')) ?>" class="btn btn--brand btn--md">
            <?= icon('arrow-left', 'icon btn__icon') ?>
            <span>Return to Store</span>
          </a>
        </div>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page_title       = 'Order Confirmed: ' . $order['order_id'] . ' — Prospect Digital Store';
$page_description = 'Your order has been received and confirmed. Review your receipt and dispatch timeline.';
$body_class       = 'page-order-confirmation';

$breadcrumbs = [
    ['name' => 'Store',        'url' => 'store'],
    ['name' => 'Confirmation', 'url' => ''],
];

require __DIR__ . '/includes/header.php';
?>

<div class="store-ambient-wrap">
  <div class="contact-tech-grid" aria-hidden="true"></div>

  <section class="section">
    <div class="container container--narrow">
      <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>

      <!-- Confirmation Card -->
      <div class="order-confirmed-card" data-reveal>
        <div class="order-confirmed-badge">
          <div class="order-confirmed-check">✓</div>
          <h1 class="order-confirmed-title">Thank You, Your Order is Confirmed!</h1>
          <p class="order-confirmed-sub">
            We have sent an order summary and invoice confirmation to <strong><?= e($order['customer']['email']) ?></strong>.
          </p>
        </div>

        <div class="order-meta-pill-box">
          <div>
            <span class="order-meta-label">Order Reference:</span>
            <strong class="order-meta-val" id="orderRefText"><?= e($order['order_id']) ?></strong>
          </div>
          <button type="button" class="btn-copy-ref" id="copyOrderRefBtn" title="Copy reference code">
            Copy
          </button>
        </div>

        <!-- 3-Step Delivery Timeline -->
        <div class="order-timeline-box">
          <div class="order-timeline-step is-complete">
            <span class="order-timeline-dot"></span>
            <span class="order-timeline-title">Order Placed</span>
            <span class="order-timeline-time"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
          </div>
          <div class="order-timeline-step is-active">
            <span class="order-timeline-dot"></span>
            <span class="order-timeline-title">Quality Verification</span>
            <span class="order-timeline-time">In Progress</span>
          </div>
          <div class="order-timeline-step">
            <span class="order-timeline-dot"></span>
            <span class="order-timeline-title">Insured Dispatch</span>
            <span class="order-timeline-time">Est. 2–3 Days</span>
          </div>
        </div>

        <!-- Order Breakdown Grid -->
        <div class="order-breakdown-grid">
          <div class="order-breakdown-cell">
            <h3>Customer Details</h3>
            <p>
              <strong><?= e($order['customer']['name']) ?></strong><br>
              <?= e($order['customer']['email']) ?><br>
              <?= e($order['customer']['phone']) ?>
            </p>
          </div>

          <div class="order-breakdown-cell">
            <h3>Shipping Address</h3>
            <address>
              <?= e($order['shipping_address']['line1']) ?><br>
              <?php if (!empty($order['shipping_address']['line2'])): ?>
                <?= e($order['shipping_address']['line2']) ?><br>
              <?php endif; ?>
              <?= e($order['shipping_address']['city']) ?>, <?= e($order['shipping_address']['state']) ?> — <?= e($order['shipping_address']['pincode']) ?><br>
              <?= e($order['shipping_address']['country']) ?>
            </address>
          </div>

          <div class="order-breakdown-cell">
            <h3>Payment Method</h3>
            <p>
              <strong><?= strtoupper(e(str_replace('_', ' ', $order['payment_method']))) ?></strong><br>
              Status: <span class="status-badge badge--closed"><?= ucfirst(e($order['payment_status'])) ?></span>
            </p>
          </div>
        </div>

        <!-- Items Table -->
        <div class="order-items-table-wrap">
          <table class="order-items-table">
            <thead>
              <tr>
                <th>Item Description</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Unit Price</th>
                <th style="text-align:right;">Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($order['items'] as $item):
                  $p = $item['product'];
              ?>
                <tr>
                  <td>
                    <strong><?= e($p['name']) ?></strong>
                    <span style="display:block;font-size:0.75rem;color:var(--ink-muted);">SKU: <?= e($p['sku']) ?></span>
                  </td>
                  <td style="text-align:center;"><?= $item['quantity'] ?></td>
                  <td style="text-align:right;"><?= e(store_format_currency($item['unit_price'])) ?></td>
                  <td style="text-align:right;"><strong><?= e(store_format_currency($item['line_total'])) ?></strong></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" style="text-align:right;">Subtotal:</td>
                <td style="text-align:right;"><?= e(store_format_currency($order['subtotal'])) ?></td>
              </tr>
              <tr>
                <td colspan="3" style="text-align:right;">GST (18%):</td>
                <td style="text-align:right;"><?= e(store_format_currency($order['tax_amount'])) ?></td>
              </tr>
              <tr>
                <td colspan="3" style="text-align:right;">Shipping:</td>
                <td style="text-align:right;"><?= $order['shipping'] == 0 ? 'FREE' : e(store_format_currency($order['shipping'])) ?></td>
              </tr>
              <?php if ($order['discount'] > 0): ?>
                <tr>
                  <td colspan="3" style="text-align:right;color:#059669;">Discount:</td>
                  <td style="text-align:right;color:#059669;">- <?= e(store_format_currency($order['discount'])) ?></td>
                </tr>
              <?php endif; ?>
              <tr class="order-total-row">
                <td colspan="3" style="text-align:right;font-weight:800;font-size:1.1rem;">Total Paid:</td>
                <td style="text-align:right;font-weight:800;font-size:1.1rem;color:var(--brand);"><?= e(store_format_currency($order['grand_total'])) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Action Buttons -->
        <div class="order-actions-row">
          <button type="button" class="btn btn--outline btn--md" onclick="window.print();">
            <?= icon('box', 'icon btn__icon') ?>
            <span>Print Invoice / Receipt</span>
          </button>
          <a href="<?= e(url('store')) ?>" class="btn btn--brand btn--md">
            <?= icon('arrow-left', 'icon btn__icon') ?>
            <span>Continue Shopping</span>
          </a>
        </div>

      </div>
    </div>
  </section>
</div>

<script>
  // Copy Order Reference interaction
  document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('copyOrderRefBtn');
    var ref = document.getElementById('orderRefText');
    if (btn && ref) {
      btn.addEventListener('click', function() {
        if (navigator.clipboard) {
          navigator.clipboard.writeText(ref.textContent.trim());
          var orig = btn.textContent;
          btn.textContent = 'Copied!';
          setTimeout(function() { btn.textContent = orig; }, 2000);
        }
      });
    }
  });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
