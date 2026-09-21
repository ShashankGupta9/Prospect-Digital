<?php
/**
 * Prospect Digital — Admin Customer Orders Management
 * ---------------------------------------------------------------------------
 * Real-time order monitoring, status updates, payment tracking, and line item receipts.
 * Backed by MySQL database tables `store_orders` and `store_order_items`.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/store-functions.php';

$page_title = 'Customer Orders';
$active_nav = 'orders';
$csrf_token = admin_csrf_token();
$db = store_db();

// ---------------------------------------------------------------------------
// 1. POST ACTION HANDLER: UPDATE ORDER STATUS / PAYMENT
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (!admin_verify_csrf($_POST['csrf_token'] ?? '')) {
        admin_set_flash('danger', 'Security validation failed. Please refresh and try again.');
        header('Location: ' . url('admin/orders.php'));
        exit;
    }

    if ($action === 'update_status') {
        $order_id = (int) ($_POST['order_id'] ?? 0);
        $order_status = trim((string) ($_POST['order_status'] ?? ''));
        $payment_status = trim((string) ($_POST['payment_status'] ?? ''));

        $allowed_order_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        $allowed_payment_statuses = ['pending', 'authorized', 'paid', 'failed', 'refunded'];

        if ($order_id > 0 && in_array($order_status, $allowed_order_statuses, true)) {
            $p_status = in_array($payment_status, $allowed_payment_statuses, true) ? $payment_status : null;
            if (store_update_order_status($order_id, $order_status, $p_status)) {
                admin_set_flash('success', 'Order status updated successfully.');
            } else {
                admin_set_flash('danger', 'Failed to update order status.');
            }
        } else {
            admin_set_flash('danger', 'Invalid order parameters supplied.');
        }

        $redirect_to = !empty($_POST['return_to_detail']) ? url('admin/orders.php?order_num=' . urlencode((string) ($_POST['order_number'] ?? ''))) : url('admin/orders.php');
        header('Location: ' . $redirect_to);
        exit;
    }
}

// ---------------------------------------------------------------------------
// 2. QUERY METRICS & REVENUE STATS
// ---------------------------------------------------------------------------
try {
    $total_orders_count = (int) $db->query("SELECT COUNT(*) FROM `store_orders`")->fetchColumn();
    $pending_fulfillment_count = (int) $db->query("SELECT COUNT(*) FROM `store_orders` WHERE `order_status` IN ('pending', 'confirmed', 'processing')")->fetchColumn();
    $delivered_count = (int) $db->query("SELECT COUNT(*) FROM `store_orders` WHERE `order_status` = 'delivered'")->fetchColumn();
    $total_revenue = (float) $db->query("SELECT COALESCE(SUM(`total_amount`), 0.00) FROM `store_orders` WHERE `payment_status` IN ('authorized', 'paid')")->fetchColumn();
} catch (PDOException $e) {
    $total_orders_count = 0;
    $pending_fulfillment_count = 0;
    $delivered_count = 0;
    $total_revenue = 0.00;
}

// ---------------------------------------------------------------------------
// 3. DETAIL VIEW CHECK
// ---------------------------------------------------------------------------
$viewing_order_num = trim((string) ($_GET['order_num'] ?? ''));
$selected_order = null;
if ($viewing_order_num !== '') {
    $selected_order = store_get_order($viewing_order_num);
}

// ---------------------------------------------------------------------------
// 4. LIST FILTERS & PAGINATION
// ---------------------------------------------------------------------------
$filter_q       = trim((string) ($_GET['q'] ?? ''));
$filter_status  = trim((string) ($_GET['status'] ?? 'all'));
$filter_payment = trim((string) ($_GET['payment_status'] ?? 'all'));

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$results = store_get_all_orders([
    'q'              => $filter_q,
    'status'         => $filter_status,
    'payment_status' => $filter_payment,
], $limit, $offset);

$orders_list = $results['orders'];
$total_matching_orders = $results['total'];
$total_pages = max(1, (int) ceil($total_matching_orders / $limit));

require __DIR__ . '/includes/header.php';
?>

<?php if ($selected_order): ?>
  <!-- =========================================================================
       DETAIL VIEW: SINGLE ORDER RECEIPT & MANAGEMENT
       ========================================================================= -->
  <div class="page-header">
    <div>
      <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom: 0.35rem;">
        <h1 class="page-title">Order <?= htmlspecialchars($selected_order['order_number'], ENT_QUOTES, 'UTF-8') ?></h1>
        <span class="status-badge" style="background: var(--admin-brand-tint); color: var(--admin-brand); font-family: monospace; font-size: 11px;">
          <?= htmlspecialchars($selected_order['order_status'], ENT_QUOTES, 'UTF-8') ?>
        </span>
      </div>
      <p class="page-subtitle">Placed on <?= date('D, d M Y · h:i A', strtotime($selected_order['created_at'])) ?> via <?= strtoupper(htmlspecialchars($selected_order['payment_method'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <div style="display:flex; gap:0.75rem;">
      <a href="<?= url('admin/orders.php') ?>" class="btn btn--outline">
        &larr; Back to All Orders
      </a>
      <button type="button" class="btn btn--secondary" onclick="window.print();">
        Print Receipt
      </button>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start; margin-bottom: 3rem;">
    <!-- Left Column: Items & Customer Details -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
      <!-- Line Items Card -->
      <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--admin-border); background: var(--admin-surface-subtle); display: flex; justify-content: space-between; align-items: center;">
          <h2 style="font-size: 1rem; font-weight: 800; margin: 0; color: var(--admin-text-primary);">
            Purchased Line Items (<?= count($selected_order['items']) ?>)
          </h2>
          <span style="font-size: 12px; color: var(--admin-text-muted);">Prices locked at time of checkout</span>
        </div>
        <table class="table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr>
              <th>Item</th>
              <th>SKU</th>
              <th style="text-align: right;">Unit Price</th>
              <th style="text-align: center;">Qty</th>
              <th style="text-align: right;">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($selected_order['items'] as $item): ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($item['product']['name'] ?? 'Product', ENT_QUOTES, 'UTF-8') ?></strong>
                </td>
                <td>
                  <span style="font-family: monospace; font-size: 11.5px; color: var(--admin-text-muted);">
                    <?= htmlspecialchars($item['product']['sku'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </td>
                <td style="text-align: right;">
                  <?= store_format_currency((float) $item['unit_price'], true) ?>
                </td>
                <td style="text-align: center; font-weight: 700;">
                  <?= (int) $item['quantity'] ?>
                </td>
                <td style="text-align: right; font-weight: 800; color: var(--admin-text-primary);">
                  <?= store_format_currency((float) $item['line_total'], true) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Order Totals Recap -->
        <div style="padding: 1.5rem; background: #fafbfc; border-top: 1px solid var(--admin-border); display: flex; justify-content: flex-end;">
          <div style="width: 280px; display: flex; flex-direction: column; gap: 0.65rem;">
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--admin-text-secondary);">
              <span>Subtotal:</span>
              <strong><?= store_format_currency($selected_order['subtotal'], true) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--admin-text-secondary);">
              <span>GST (18%):</span>
              <strong><?= store_format_currency($selected_order['tax_amount'], true) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--admin-text-secondary);">
              <span>Shipping:</span>
              <strong><?= $selected_order['shipping'] > 0 ? store_format_currency($selected_order['shipping'], true) : 'FREE' ?></strong>
            </div>
            <?php if ($selected_order['discount'] > 0): ?>
              <div style="display: flex; justify-content: space-between; font-size: 13px; color: #059669;">
                <span>Discount:</span>
                <strong>-<?= store_format_currency($selected_order['discount'], true) ?></strong>
              </div>
            <?php endif; ?>
            <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; color: var(--admin-text-primary); border-top: 1.5px dashed var(--admin-border); padding-top: 0.75rem;">
              <span>Grand Total:</span>
              <span><?= store_format_currency($selected_order['grand_total'], true) ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Customer & Delivery Address Card -->
      <div class="card">
        <h2 style="font-size: 1rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--admin-text-primary); border-bottom: 1px solid var(--admin-border); padding-bottom: 0.65rem;">
          Customer &amp; Shipping Information
        </h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
          <div>
            <h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-text-muted); margin-bottom: 0.5rem;">Contact Details</h3>
            <p style="margin-bottom: 0.25rem;"><strong><?= htmlspecialchars($selected_order['customer']['name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
            <p style="margin-bottom: 0.25rem; color: var(--admin-text-secondary); font-size: 13px;"><?= htmlspecialchars($selected_order['customer']['email'], ENT_QUOTES, 'UTF-8') ?></p>
            <p style="color: var(--admin-text-secondary); font-size: 13px;"><?= htmlspecialchars($selected_order['customer']['phone'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
          <div>
            <h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-text-muted); margin-bottom: 0.5rem;">Delivery Destination</h3>
            <p style="margin-bottom: 0.25rem; font-size: 13px;"><?= htmlspecialchars($selected_order['shipping_address']['line1'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php if (!empty($selected_order['shipping_address']['line2'])): ?>
              <p style="margin-bottom: 0.25rem; font-size: 13px;"><?= htmlspecialchars($selected_order['shipping_address']['line2'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <p style="margin-bottom: 0.25rem; font-size: 13px;"><?= htmlspecialchars($selected_order['shipping_address']['city'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($selected_order['shipping_address']['state'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($selected_order['shipping_address']['pincode'], ENT_QUOTES, 'UTF-8') ?></p>
            <p style="font-size: 13px; color: var(--admin-text-muted);"><?= htmlspecialchars($selected_order['shipping_address']['country'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </div>

        <?php if (!empty($selected_order['notes'])): ?>
          <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--admin-border);">
            <h4 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-text-muted); margin-bottom: 0.4rem;">Customer Delivery Instructions</h4>
            <p style="font-size: 13px; color: var(--admin-text-secondary); font-style: italic; margin: 0;">
              "<?= htmlspecialchars($selected_order['notes'], ENT_QUOTES, 'UTF-8') ?>"
            </p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right Column: Status & Fulfillment Controls -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div class="card" style="position: sticky; top: 90px;">
        <h2 style="font-size: 1rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--admin-text-primary); border-bottom: 1px solid var(--admin-border); padding-bottom: 0.65rem;">
          Order Management
        </h2>
        <form method="POST" action="<?= url('admin/orders.php') ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="action" value="update_status">
          <input type="hidden" name="order_id" value="<?= (int) $selected_order['id'] ?>">
          <input type="hidden" name="order_number" value="<?= htmlspecialchars($selected_order['order_number'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="return_to_detail" value="1">

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="orderStatus">Fulfillment Status</label>
            <select id="orderStatus" name="order_status" class="form-control">
              <option value="pending" <?= $selected_order['order_status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
              <option value="confirmed" <?= $selected_order['order_status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
              <option value="processing" <?= $selected_order['order_status'] === 'processing' ? 'selected' : '' ?>>Processing / Packing</option>
              <option value="shipped" <?= $selected_order['order_status'] === 'shipped' ? 'selected' : '' ?>>Shipped / Out for Delivery</option>
              <option value="delivered" <?= $selected_order['order_status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
              <option value="cancelled" <?= $selected_order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 1.5rem;">
            <label for="paymentStatus">Payment Status</label>
            <select id="paymentStatus" name="payment_status" class="form-control">
              <option value="pending" <?= $selected_order['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="authorized" <?= $selected_order['payment_status'] === 'authorized' ? 'selected' : '' ?>>Authorized</option>
              <option value="paid" <?= $selected_order['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid (Settled)</option>
              <option value="failed" <?= $selected_order['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
              <option value="refunded" <?= $selected_order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
            </select>
          </div>

          <button type="submit" class="btn btn--primary" style="width: 100%;">
            Update Order Status
          </button>
        </form>
      </div>
    </div>
  </div>

<?php else: ?>
  <!-- =========================================================================
       MAIN LIST VIEW: KPI CARDS + FILTERS + ORDERS TABLE
       ========================================================================= -->
  <div class="page-header">
    <div>
      <h1 class="page-title">Customer Orders</h1>
      <p class="page-subtitle">Track orders, customer details, fulfillment status, and financial settlements.</p>
    </div>
    <div style="display:flex; gap:0.75rem;">
      <a href="<?= url('admin/store.php') ?>" class="btn btn--outline">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        Manage Products
      </a>
      <a href="<?= url('store') ?>" target="_blank" class="btn btn--secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        View Store Catalog
      </a>
    </div>
  </div>

  <!-- KPI Stats Row -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-card__icon" style="background: var(--admin-brand-tint); color: var(--admin-brand);">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      </div>
      <div>
        <div class="stat-card__value"><?= number_format($total_orders_count) ?></div>
        <div class="stat-card__label">Total Orders</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card__icon" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div>
        <div class="stat-card__value"><?= number_format($pending_fulfillment_count) ?></div>
        <div class="stat-card__label">Pending Fulfillment</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card__icon" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div>
        <div class="stat-card__value"><?= number_format($delivered_count) ?></div>
        <div class="stat-card__label">Completed / Delivered</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card__icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div class="stat-card__value"><?= store_format_currency($total_revenue) ?></div>
        <div class="stat-card__label">Paid Store Revenue</div>
      </div>
    </div>
  </div>

  <!-- Search & Filter Controls -->
  <div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="<?= url('admin/orders.php') ?>" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
      <div style="flex: 2; min-width: 240px;">
        <input type="text" name="q" class="form-control" placeholder="Search order reference, customer, email, phone..." value="<?= htmlspecialchars($filter_q, ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div style="flex: 1; min-width: 170px;">
        <select name="status" class="form-control">
          <option value="all">All Fulfillment Statuses</option>
          <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="confirmed" <?= $filter_status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
          <option value="processing" <?= $filter_status === 'processing' ? 'selected' : '' ?>>Processing</option>
          <option value="shipped" <?= $filter_status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
          <option value="delivered" <?= $filter_status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
          <option value="cancelled" <?= $filter_status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
      </div>

      <div style="flex: 1; min-width: 170px;">
        <select name="payment_status" class="form-control">
          <option value="all">All Payment Statuses</option>
          <option value="pending" <?= $filter_payment === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="authorized" <?= $filter_payment === 'authorized' ? 'selected' : '' ?>>Authorized</option>
          <option value="paid" <?= $filter_payment === 'paid' ? 'selected' : '' ?>>Paid</option>
          <option value="failed" <?= $filter_payment === 'failed' ? 'selected' : '' ?>>Failed</option>
          <option value="refunded" <?= $filter_payment === 'refunded' ? 'selected' : '' ?>>Refunded</option>
        </select>
      </div>

      <div style="display: flex; gap: 0.5rem;">
        <button type="submit" class="btn btn--secondary">Filter</button>
        <?php if ($filter_q !== '' || $filter_status !== 'all' || $filter_payment !== 'all'): ?>
          <a href="<?= url('admin/orders.php') ?>" class="btn btn--outline">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Orders Data Table -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <?php if (empty($orders_list)): ?>
      <div style="text-align: center; padding: 4.5rem 2rem;">
        <div style="width: 64px; height: 64px; border-radius: 16px; background: var(--admin-brand-tint); color: var(--admin-brand); display: grid; place-items: center; margin: 0 auto 1.25rem;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--admin-text-primary); margin-bottom: 0.5rem;">
          <?= $total_orders_count === 0 ? 'No Customer Orders Placed Yet' : 'No Orders Match Your Filter' ?>
        </h3>
        <p style="color: var(--admin-text-secondary); max-width: 440px; margin: 0 auto 1.5rem; font-size: 13.5px;">
          <?= $total_orders_count === 0
            ? 'When customers place orders through the public store, their checkout records, shipping addresses, and payment details will appear here automatically.'
            : 'Try modifying your search criteria or clearing active filters to see all recorded orders.' ?>
        </p>
        <?php if ($total_orders_count === 0): ?>
          <a href="<?= url('store') ?>" target="_blank" class="btn btn--primary">
            Visit Public Store
          </a>
        <?php else: ?>
          <a href="<?= url('admin/orders.php') ?>" class="btn btn--secondary">
            Clear Filters
          </a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr>
              <th>Order Number</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Fulfillment</th>
              <th style="text-align: right; width: 140px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders_list as $ord): ?>
              <?php
                $o_num = (string) $ord['order_number'];
                $o_cust = (string) $ord['customer_name'];
                $o_email = (string) $ord['customer_email'];
                $o_date = date('d M Y, h:i A', strtotime((string) $ord['created_at']));
                $o_total = (float) $ord['total_amount'];
                $o_pay_status = (string) $ord['payment_status'];
                $o_order_status = (string) $ord['order_status'];

                $pay_badge_style = match ($o_pay_status) {
                    'paid'       => 'background: rgba(5, 150, 105, 0.1); color: #059669; border: 1px solid rgba(5, 150, 105, 0.3);',
                    'authorized' => 'background: rgba(37, 99, 235, 0.1); color: #2563eb; border: 1px solid rgba(37, 99, 235, 0.3);',
                    'refunded'   => 'background: rgba(100, 116, 139, 0.1); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.3);',
                    'failed'     => 'background: rgba(220, 38, 38, 0.1); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.3);',
                    default      => 'background: rgba(217, 119, 6, 0.1); color: #d97706; border: 1px solid rgba(217, 119, 6, 0.3);',
                };

                $fulfillment_badge_style = match ($o_order_status) {
                    'delivered'  => 'background: rgba(5, 150, 105, 0.1); color: #059669; border: 1px solid rgba(5, 150, 105, 0.3);',
                    'shipped'    => 'background: rgba(37, 99, 235, 0.1); color: #2563eb; border: 1px solid rgba(37, 99, 235, 0.3);',
                    'cancelled'  => 'background: rgba(220, 38, 38, 0.1); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.3);',
                    'processing' => 'background: rgba(124, 58, 237, 0.1); color: #7c3aed; border: 1px solid rgba(124, 58, 237, 0.3);',
                    default      => 'background: rgba(217, 119, 6, 0.1); color: #d97706; border: 1px solid rgba(217, 119, 6, 0.3);',
                };
              ?>
              <tr>
                <td>
                  <a href="<?= url('admin/orders.php?order_num=' . urlencode($o_num)) ?>" style="font-family: monospace; font-weight: 700; color: var(--admin-brand);">
                    <?= htmlspecialchars($o_num, ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </td>
                <td>
                  <div style="display:flex; flex-direction:column; gap:2px;">
                    <span style="font-weight: 700; color: var(--admin-text-primary); font-size: 13.5px;"><?= htmlspecialchars($o_cust, ENT_QUOTES, 'UTF-8') ?></span>
                    <span style="font-size: 11.5px; color: var(--admin-text-muted);"><?= htmlspecialchars($o_email, ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                </td>
                <td style="font-size: 12.5px; color: var(--admin-text-secondary); white-space: nowrap;">
                  <?= $o_date ?>
                </td>
                <td>
                  <span style="font-weight: 800; font-size: 13.5px; color: var(--admin-text-primary);">
                    <?= store_format_currency($o_total, true) ?>
                  </span>
                </td>
                <td>
                  <span class="status-badge" style="<?= $pay_badge_style ?>">
                    <?= ucfirst($o_pay_status) ?>
                  </span>
                </td>
                <td>
                  <span class="status-badge" style="<?= $fulfillment_badge_style ?>">
                    <?= ucfirst($o_order_status) ?>
                  </span>
                </td>
                <td style="text-align: right;">
                  <a href="<?= url('admin/orders.php?order_num=' . urlencode($o_num)) ?>" class="btn btn--secondary btn--sm">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Manage
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
