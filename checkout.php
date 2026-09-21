<?php
/**
 * Prospect Digital — Store Checkout Page
 * ---------------------------------------------------------------------------
 * Complete multi-step checkout flow:
 *   • Customer information with automatic auth prefill
 *   • Shipping & Delivery address with validation
 *   • Payment method selection structure (UPI, Cards, Net Banking, COD/Wire)
 *   • Real-time order review sidebar
 *   • Order generation and redirection to confirmation receipt
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/store-functions.php';

$cur_user = current_user();
if (!$cur_user) {
    header('Location: ' . url('login?return=' . urlencode(url('checkout'))));
    exit;
}

$cart = store_cart_get();

// Redirect to cart if empty
if ($cart['is_empty'] && ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ' . url('cart'));
    exit;
}

$cur_user = current_user();

$checkout_errors       = [];
$checkout_field_errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $result = store_process_checkout($_POST);
    if ($result['ok']) {
        header('Location: ' . url('order-confirmation?order_id=' . urlencode($result['order_id'])));
        exit;
    }

    $checkout_errors       = $result['errors'] ?? [];
    $checkout_field_errors = $result['field_errors'] ?? [];
}

$val = static function (string $key, string $default = '') use ($cur_user): string {
    if (isset($_POST[$key])) {
        return e((string) $_POST[$key]);
    }
    if ($cur_user) {
        if ($key === 'customer_name')  return e($cur_user['name']);
        if ($key === 'customer_email') return e($cur_user['email']);
        if ($key === 'customer_phone') return e($cur_user['phone'] ?? '');
    }
    return e($default);
};

$page_title       = 'Secure Checkout — Prospect Digital Store';
$page_description = 'Complete your purchase securely. Enter delivery address, choose your payment method, and confirm your order.';
$body_class       = 'page-store-checkout';

$breadcrumbs = [
    ['name' => 'Store',    'url' => 'store'],
    ['name' => 'Cart',     'url' => 'cart'],
    ['name' => 'Checkout', 'url' => 'checkout'],
];

require __DIR__ . '/includes/header.php';
?>

<div class="store-ambient-wrap">
  <div class="contact-tech-grid" aria-hidden="true"></div>

  <section class="section store-checkout-section">
    <div class="container">
      <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>

      <div class="cart-header-row" data-reveal>
        <div>
          <h1 class="cart-page-title">Secure Checkout</h1>
          <p class="cart-page-sub">Review your items and complete delivery details</p>
        </div>
        <div class="checkout-security-indicator">
          <?= icon('shield', 'icon') ?>
          <span>256-Bit SSL Encrypted Transaction</span>
        </div>
      </div>

      <?php if (!empty($checkout_errors)): ?>
        <div class="alert alert--error" role="alert" style="margin-bottom:2rem;" data-reveal>
          <p class="alert__title">Please fix the following issues:</p>
          <ul class="alert__list">
            <?php foreach ($checkout_errors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="<?= e(url('checkout')) ?>" method="post" class="checkout-form" id="checkoutForm">
        <div class="checkout-layout-grid">

          <!-- Left Column: Checkout Information Steps -->
          <div class="checkout-main-column">

            <!-- Step 1: Customer Contact Info -->
            <div class="checkout-card" data-reveal>
              <div class="checkout-card__header">
                <span class="checkout-step-num">1</span>
                <div>
                  <h2 class="checkout-card__title">Customer Contact Information</h2>
                  <p class="checkout-card__sub">We will send your order receipt and tracking updates here.</p>
                </div>
              </div>

              <?php if (!current_user()): ?>
                <div class="checkout-auth-banner" style="background: rgba(239, 68, 68, 0.04); border: 1px solid rgba(239, 68, 68, 0.18); border-radius: 12px; padding: 0.9rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.85rem;">
                  <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); color: var(--brand); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                      <?= icon('user', 'icon') ?>
                    </div>
                    <div>
                      <strong style="font-size: 0.88rem; color: var(--ink);">Have a client account?</strong>
                      <p style="font-size: 0.8rem; color: var(--text3); margin: 0;">Sign in to autofill your shipping details and save this purchase to your profile.</p>
                    </div>
                  </div>
                  <div style="display: flex; gap: 0.5rem;">
                    <a href="<?= e(url('login?return=' . urlencode(url('checkout')))) ?>" class="btn btn--outline btn--sm">Sign In</a>
                    <a href="<?= e(url('signup?return=' . urlencode(url('checkout')))) ?>" class="btn btn--brand btn--sm">Sign Up</a>
                  </div>
                </div>
              <?php endif; ?>

              <div class="modern-field-row modern-field-row--2">
                <div class="modern-field">
                  <label class="modern-field__label" for="customer_name">
                    Full Name <span class="req">*</span>
                  </label>
                  <div class="modern-input-wrap">
                    <input type="text"
                           id="customer_name"
                           name="customer_name"
                           class="modern-input"
                           value="<?= $val('customer_name') ?>"
                           required
                           autocomplete="name"
                           placeholder="Rahul Sharma">
                    <span class="modern-input-icon"><?= icon('user', 'icon') ?></span>
                  </div>
                </div>

                <div class="modern-field">
                  <label class="modern-field__label" for="customer_phone">
                    Phone Number <span class="req">*</span>
                  </label>
                  <div class="modern-input-wrap">
                    <input type="tel"
                           id="customer_phone"
                           name="customer_phone"
                           class="modern-input"
                           value="<?= $val('customer_phone') ?>"
                           required
                           autocomplete="tel"
                           placeholder="+91 98260 00000">
                    <span class="modern-input-icon"><?= icon('phone', 'icon') ?></span>
                  </div>
                </div>
              </div>

              <div class="modern-field">
                <label class="modern-field__label" for="customer_email">
                  Work / Personal E-mail <span class="req">*</span>
                </label>
                <div class="modern-input-wrap">
                  <input type="email"
                         id="customer_email"
                         name="customer_email"
                         class="modern-input"
                         value="<?= $val('customer_email') ?>"
                         required
                         autocomplete="email"
                         placeholder="rahul@company.com">
                  <span class="modern-input-icon"><?= icon('mail', 'icon') ?></span>
                </div>
              </div>
            </div>

            <!-- Step 2: Shipping & Delivery Address -->
            <div class="checkout-card" data-reveal>
              <div class="checkout-card__header">
                <span class="checkout-step-num">2</span>
                <div>
                  <h2 class="checkout-card__title">Shipping &amp; Delivery Address</h2>
                  <p class="checkout-card__sub">Where should we deliver your hardware or documentation package?</p>
                </div>
              </div>

              <div class="modern-field" style="margin-bottom:1.15rem;">
                <label class="modern-field__label" for="address_line1">
                  Street Address / Company Unit <span class="req">*</span>
                </label>
                <div class="modern-input-wrap">
                  <input type="text"
                         id="address_line1"
                         name="address_line1"
                         class="modern-input"
                         value="<?= $val('address_line1') ?>"
                         required
                         autocomplete="street-address"
                         placeholder="Plot 42, Innovation Park, Building A">
                  <span class="modern-input-icon"><?= icon('pin', 'icon') ?></span>
                </div>
              </div>

              <div class="modern-field" style="margin-bottom:1.15rem;">
                <label class="modern-field__label" for="address_line2">
                  Apartment, Suite, Landmark <span class="field__hint">(optional)</span>
                </label>
                <div class="modern-input-wrap">
                  <input type="text"
                         id="address_line2"
                         name="address_line2"
                         class="modern-input"
                         value="<?= $val('address_line2') ?>"
                         placeholder="Near Chetak Bridge, Zone-1">
                  <span class="modern-input-icon"><?= icon('pin', 'icon') ?></span>
                </div>
              </div>

              <div class="modern-field-row modern-field-row--2">
                <div class="modern-field">
                  <label class="modern-field__label" for="city">
                    City <span class="req">*</span>
                  </label>
                  <input type="text"
                         id="city"
                         name="city"
                         class="modern-input"
                         style="padding-left:1rem;"
                         value="<?= $val('city', 'Bhopal') ?>"
                         required
                         autocomplete="address-level2"
                         placeholder="Bhopal">
                </div>

                <div class="modern-field">
                  <label class="modern-field__label" for="state">
                    State <span class="req">*</span>
                  </label>
                  <input type="text"
                         id="state"
                         name="state"
                         class="modern-input"
                         style="padding-left:1rem;"
                         value="<?= $val('state', 'Madhya Pradesh') ?>"
                         required
                         autocomplete="address-level1"
                         placeholder="Madhya Pradesh">
                </div>
              </div>

              <div class="modern-field-row modern-field-row--2" style="margin-top:1.15rem;">
                <div class="modern-field">
                  <label class="modern-field__label" for="pincode">
                    PIN Code / Postal Code <span class="req">*</span>
                  </label>
                  <input type="text"
                         id="pincode"
                         name="pincode"
                         class="modern-input"
                         style="padding-left:1rem;"
                         value="<?= $val('pincode', '462011') ?>"
                         required
                         autocomplete="postal-code"
                         placeholder="462011">
                </div>

                <div class="modern-field">
                  <label class="modern-field__label" for="country">
                    Country <span class="req">*</span>
                  </label>
                  <input type="text"
                         id="country"
                         name="country"
                         class="modern-input"
                         style="padding-left:1rem;"
                         value="<?= $val('country', 'India') ?>"
                         required
                         readonly>
                </div>
              </div>
            </div>

            <!-- Step 3: Payment Method Selection -->
            <div class="checkout-card" data-reveal>
              <div class="checkout-card__header">
                <span class="checkout-step-num">3</span>
                <div>
                  <h2 class="checkout-card__title">Payment Method</h2>
                  <p class="checkout-card__sub">Select your preferred transaction channel</p>
                </div>
              </div>

              <div class="payment-methods-grid">
                <!-- UPI -->
                <label class="payment-option-card">
                  <input type="radio" name="payment_method" value="upi" checked class="payment-option-radio">
                  <div class="payment-option-body">
                    <div class="payment-option-top">
                      <span class="payment-option-name">UPI / QR Payment</span>
                      <span class="payment-option-badge">Instant 0% Fee</span>
                    </div>
                    <p class="payment-option-desc">Google Pay, PhonePe, Paytm, BHIM, and all UPI applications.</p>
                  </div>
                </label>

                <!-- Cards -->
                <label class="payment-option-card">
                  <input type="radio" name="payment_method" value="card" class="payment-option-radio">
                  <div class="payment-option-body">
                    <div class="payment-option-top">
                      <span class="payment-option-name">Credit / Debit Card</span>
                      <span class="payment-option-badge">Visa / MC / RuPay</span>
                    </div>
                    <p class="payment-option-desc">Domestic &amp; international cards protected with 256-bit 3D Secure OTP.</p>
                  </div>
                </label>

                <!-- Net Banking -->
                <label class="payment-option-card">
                  <input type="radio" name="payment_method" value="netbanking" class="payment-option-radio">
                  <div class="payment-option-body">
                    <div class="payment-option-top">
                      <span class="payment-option-name">Net Banking</span>
                      <span class="payment-option-badge">50+ Banks</span>
                    </div>
                    <p class="payment-option-desc">HDFC, ICICI, SBI, Axis, Kotak, and all major Indian banking portals.</p>
                  </div>
                </label>

                <!-- Bank Transfer / Corporate PO -->
                <label class="payment-option-card">
                  <input type="radio" name="payment_method" value="bank_transfer" class="payment-option-radio">
                  <div class="payment-option-body">
                    <div class="payment-option-top">
                      <span class="payment-option-name">Corporate PO / NEFT Wire</span>
                      <span class="payment-option-badge">Invoice B2B</span>
                    </div>
                    <p class="payment-option-desc">Direct wire transfer for verified corporate orders and GST invoicing.</p>
                  </div>
                </label>
              </div>

              <!-- Order Notes -->
              <div class="modern-field" style="margin-top:1.5rem;">
                <label class="modern-field__label" for="order_notes">
                  Order / GST Notes <span class="field__hint">(optional)</span>
                </label>
                <textarea id="order_notes"
                          name="order_notes"
                          class="modern-textarea"
                          style="min-height:75px;"
                          placeholder="Provide GSTIN number for tax credit, delivery instructions, or gate codes."><?= $val('order_notes') ?></textarea>
              </div>
            </div>

          </div>

          <!-- Right Column: Order Summary & Confirmation -->
          <aside class="checkout-sidebar">
            <div class="cart-summary-card">
              <h2 class="cart-summary-title">Order Overview</h2>

              <!-- Items mini list -->
              <div class="checkout-items-mini">
                <?php foreach ($cart['items'] as $row):
                    $p = $row['product'];
                ?>
                  <div class="checkout-item-mini">
                    <div class="checkout-item-mini__desc">
                      <span class="checkout-item-mini__name"><?= e($p['name']) ?></span>
                      <span class="checkout-item-mini__qty">Qty: <?= $row['quantity'] ?> × <?= e(store_format_currency($row['unit_price'])) ?></span>
                    </div>
                    <span class="checkout-item-mini__price"><?= e(store_format_currency($row['line_total'])) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="cart-summary-divider"></div>

              <!-- Calculations -->
              <div class="cart-summary-lines">
                <div class="cart-summary-line">
                  <span>Subtotal</span>
                  <span><?= e(store_format_currency($cart['subtotal'])) ?></span>
                </div>

                <div class="cart-summary-line">
                  <span>GST (18%)</span>
                  <span><?= e(store_format_currency($cart['tax_amount'])) ?></span>
                </div>

                <div class="cart-summary-line">
                  <span>Shipping</span>
                  <span><?= $cart['shipping'] == 0 ? '<strong style="color:#059669;">FREE</strong>' : e(store_format_currency($cart['shipping'])) ?></span>
                </div>

                <?php if ($cart['discount'] > 0): ?>
                  <div class="cart-summary-line cart-summary-line--discount">
                    <span>Discount</span>
                    <span>- <?= e(store_format_currency($cart['discount'])) ?></span>
                  </div>
                <?php endif; ?>

                <div class="cart-summary-divider"></div>

                <div class="cart-summary-line cart-summary-line--total">
                  <span>Grand Total</span>
                  <span class="cart-grand-total"><?= e(store_format_currency($cart['grand_total'])) ?></span>
                </div>
              </div>

              <!-- Submit Order Button -->
              <button type="submit" class="btn btn--brand btn--lg cart-checkout-btn" style="margin-top:1.5rem;width:100%;">
                <span>Place Order &amp; Confirm</span>
                <?= icon('arrow', 'icon btn__icon') ?>
              </button>

              <p class="checkout-terms-note">
                By placing your order, you agree to Prospect Digital's
                <a href="<?= e(url('terms')) ?>" target="_blank">Terms of Service</a> and
                <a href="<?= e(url('privacy')) ?>" target="_blank">Privacy Policy</a>.
              </p>

              <div class="cart-trust-badges">
                <div class="cart-trust-badge">
                  <?= icon('shield', 'icon') ?>
                  <span>256-Bit SSL Encrypted</span>
                </div>
                <div class="cart-trust-badge">
                  <?= icon('check', 'icon') ?>
                  <span>100% Guaranteed</span>
                </div>
              </div>

            </div>
          </aside>

        </div>
      </form>

    </div>
  </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
