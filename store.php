<?php
/**
 * Prospect Digital — Store Catalog & Landing Page
 * ---------------------------------------------------------------------------
 * Complete e-commerce store catalog featuring:
 *   • Live search, category filtering, and price/name sorting
 *   • Professional Empty-Store state ("Launching Soon" with notification signup)
 *   • Reusable product card grid structure ready for instant inventory expansion
 *   • SEO-ready Open Graph & Schema.org structured data
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/store-functions.php';

$current_search   = clean_text($_GET['q'] ?? '', 100);
$current_category = clean_text($_GET['category'] ?? 'all', 60);
$current_sort     = clean_text($_GET['sort'] ?? 'featured', 30);

$all_categories   = store_get_categories();
$raw_products     = store_get_products();
$products         = store_filter_and_sort($raw_products, $current_search, $current_category, $current_sort);

$page_title       = 'Official Hardware, Software Licenses & Developer Store — Prospect Digital';
$page_description = 'Shop industrial IoT hardware, certified software deployment licenses, and developer tools curated by Prospect Digital. Fast shipping across India with warranty and direct engineer support.';
$body_class       = 'page-store';
$hero_slug        = 'store';

$breadcrumbs = [
    ['name' => 'Store', 'url' => 'store'],
];

if ($current_category !== 'all' && isset($all_categories[$current_category])) {
    $breadcrumbs[] = ['name' => $all_categories[$current_category]['name'], 'url' => 'store?category=' . urlencode($current_category)];
}

$page_jsonld = [[
    '@context'    => 'https://schema.org',
    '@type'       => 'Store',
    'name'        => COMPANY_NAME . ' Official Store',
    'url'         => absolute_url('store'),
    'description' => $page_description,
    'telephone'   => '+' . COMPANY_PHONE_RAW,
    'address'     => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'R-52, First Floor, Gulab Vila, Zone-1, M.P. Nagar',
        'addressLocality' => COMPANY_CITY,
        'addressRegion'   => COMPANY_STATE,
        'postalCode'      => COMPANY_PINCODE,
        'addressCountry'  => 'IN',
    ],
]];

require __DIR__ . '/includes/header.php';
?>

<!-- Ambient Background Animation -->
<div class="store-ambient-wrap">
  <div class="contact-tech-grid" aria-hidden="true"></div>
  <div class="contact-orb contact-orb--1" aria-hidden="true"></div>
  <div class="contact-orb contact-orb--2" aria-hidden="true"></div>

  <!-- ==================== 1. STORE HERO & FILTERS ==================== -->
  <section class="store-hero">
    <div class="container">
      <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>

      <div class="store-hero__inner" data-reveal>
        <div class="store-hero__badge">
          <span class="pulse-dot"></span>
          <span>PROSPECT DIGITAL / OFFICIAL HARDWARE &amp; LICENSES STORE</span>
        </div>

        <h1 class="store-hero__title">
          Engineered hardware &amp; <span class="contact-title-accent">developer tools.</span>
        </h1>

        <p class="store-hero__lead">
          Browse verified industrial IoT kits, enterprise deployment license packages, and custom developer utilities backed by our engineering team in Bhopal.
        </p>

        <!-- Search & Control Bar -->
        <div class="store-controls-bar">
          <form class="store-search-form" action="<?= e(url('store')) ?>" method="get" role="search">
            <?php if ($current_category !== 'all'): ?>
              <input type="hidden" name="category" value="<?= e($current_category) ?>">
            <?php endif; ?>
            <div class="store-search-input-wrap">
              <span class="store-search-icon"><?= icon('search', 'icon') ?></span>
              <input type="search"
                     name="q"
                     class="store-search-input"
                     placeholder="Search hardware, licenses, SKUs, or modules…"
                     value="<?= e($current_search) ?>"
                     autocomplete="off"
                     aria-label="Search products">
              <?php if ($current_search !== ''): ?>
                <a href="<?= e(url('store?category=' . urlencode($current_category))) ?>" class="store-search-clear" title="Clear search">
                  <?= icon('close', 'icon') ?>
                </a>
              <?php endif; ?>
            </div>
            <button type="submit" class="btn btn--brand btn--sm store-search-btn">
              <span>Search</span>
            </button>
          </form>

          <!-- Sort Selector -->
          <div class="store-sort-wrap">
            <label for="storeSortSelect" class="store-sort-label">Sort by:</label>
            <select id="storeSortSelect" class="store-sort-select" onchange="window.location.href=this.value;">
              <?php
              $sort_options = [
                  'featured'   => 'Featured',
                  'newest'     => 'Newest First',
                  'price-asc'  => 'Price: Low to High',
                  'price-desc' => 'Price: High to Low',
                  'name-asc'   => 'Name: A to Z',
              ];
              foreach ($sort_options as $opt_key => $opt_label):
                  $q_params = ['category' => $current_category, 'sort' => $opt_key];
                  if ($current_search !== '') {
                      $q_params['q'] = $current_search;
                  }
                  $sort_url = url('store?' . http_build_query($q_params));
                  $is_sel   = ($current_sort === $opt_key);
              ?>
                <option value="<?= e($sort_url) ?>"<?= $is_sel ? ' selected' : '' ?>><?= e($opt_label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Category Chips Bar -->
        <div class="store-categories-bar" role="navigation" aria-label="Product categories">
          <?php foreach ($all_categories as $cat_slug => $cat):
              $is_active = ($current_category === $cat_slug);
              $cat_params = ['category' => $cat_slug];
              if ($current_search !== '') {
                  $cat_params['q'] = $current_search;
              }
              if ($current_sort !== 'featured') {
                  $cat_params['sort'] = $current_sort;
              }
              $cat_url = url('store?' . http_build_query($cat_params));
          ?>
            <a href="<?= e($cat_url) ?>" class="store-cat-chip<?= $is_active ? ' is-active' : '' ?>">
              <?= icon($cat['icon'] ?? 'tag', 'icon store-cat-icon') ?>
              <span><?= e($cat['name']) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== 2. PRODUCTS GRID / EMPTY STATE ==================== -->
  <section class="section store-catalog-section" id="catalog">
    <div class="container">

      <?php if (!empty($products)): ?>
        <!-- Active Product Grid (Rendered automatically once items exist) -->
        <div class="store-grid">
          <?php foreach ($products as $product): ?>
            <?php require __DIR__ . '/includes/store-product-card.php'; ?>
          <?php endforeach; ?>
        </div>

      <?php else: ?>
        <!-- =================================================================
             PROFESSIONAL EMPTY-STORE STATE
             Displayed elegantly when no products exist in the catalog yet.
             ================================================================= -->
        <div class="store-empty-panel" data-reveal>
          <div class="store-empty-card">
            <div class="store-empty-badge">
              <span class="pulse-dot"></span>
              <span>Catalog In Production</span>
            </div>

            <div class="store-empty-icon-wrap">
              <?= icon('shopping-bag', 'icon store-empty-icon') ?>
              <span class="store-empty-sparkle"><?= icon('sparkle', 'icon') ?></span>
            </div>

            <h2 class="store-empty-title">
              Our Official Store is Launching Soon.
            </h2>

            <p class="store-empty-text">
              We are currently packaging and curating our verified industrial IoT controllers, IoT development modules, enterprise software deployment licenses, and diagnostic appliances.
            </p>

            <div class="store-empty-info-box">
              <p>
                <strong>The store infrastructure, secure checkout, and warranty tracking systems are fully active.</strong>
                Items will be stocked in batches shortly.
              </p>
            </div>

            <!-- Priority Notify Form -->
            <div class="store-notify-wrap">
              <p class="store-notify-label">
                Get notified when the next hardware batch &amp; license keys become available:
              </p>
              <form class="store-notify-form" data-store-notify-form>
                <div class="store-notify-input-group">
                  <input type="email"
                         name="notify_email"
                         class="store-notify-input"
                         placeholder="Enter your work email address…"
                         required
                         autocomplete="email">
                  <button type="submit" class="btn btn--brand btn--sm store-notify-btn">
                    <span>Notify Me</span>
                    <?= icon('arrow', 'icon btn__icon') ?>
                  </button>
                </div>
                <p class="store-notify-feedback" style="display:none;"></p>
              </form>
            </div>

            <!-- Upcoming Category Showcase Preview -->
            <div class="store-preview-categories">
              <h3 class="store-preview-heading">Upcoming Categories in This Store:</h3>
              <div class="store-preview-grid">
                <div class="store-preview-item">
                  <div class="store-preview-num">01</div>
                  <div class="store-preview-icon"><?= icon('cpu', 'icon') ?></div>
                  <h4>Hardware &amp; IoT Kits</h4>
                  <p>Industrial PLC controllers, Modbus gateways, GSM telemetry boards, and custom sensor enclosures.</p>
                </div>
                <div class="store-preview-item">
                  <div class="store-preview-num">02</div>
                  <div class="store-preview-icon"><?= icon('shield', 'icon') ?></div>
                  <h4>Software Licenses</h4>
                  <p>Self-hosted enterprise licenses, ERP/CRM deployment tiers, and automated workflow engine keys.</p>
                </div>
                <div class="store-preview-item">
                  <div class="store-preview-num">03</div>
                  <div class="store-preview-icon"><?= icon('code', 'icon') ?></div>
                  <h4>Developer &amp; API Tools</h4>
                  <p>Hardware debuggers, serial adapters, testing suites, and dedicated webhook integration bridges.</p>
                </div>
                <div class="store-preview-item">
                  <div class="store-preview-num">04</div>
                  <div class="store-preview-icon"><?= icon('cloud', 'icon') ?></div>
                  <h4>Cloud Appliances</h4>
                  <p>Pre-configured on-premise cloud servers and secure localized private backup appliances.</p>
                </div>
              </div>
            </div>

            <!-- Alternative Action: Custom Solutions Consultation -->
            <div class="store-empty-cta-box">
              <div class="store-empty-cta-copy">
                <h4>Need bulk hardware or a tailored software build right now?</h4>
                <p>Our engineering team in Bhopal designs custom embedded boards, automation systems, and enterprise software to order.</p>
              </div>
              <div class="store-empty-cta-actions">
                <a class="btn btn--brand btn--md" href="<?= e(url('contact')) ?>">
                  <span>Talk to Engineering</span>
                  <?= icon('arrow', 'icon btn__icon') ?>
                </a>
                <a class="btn btn--outline btn--md" href="<?= e(url('projects')) ?>">
                  <span>View Our Custom Work</span>
                </a>
              </div>
            </div>

          </div>
        </div>
      <?php endif; ?>

    </div>
  </section>

  <!-- ==================== 3. STORE VALUE PROPOSITIONS ==================== -->
  <section class="store-trust-section">
    <div class="container">
      <div class="store-trust-grid">
        <div class="store-trust-card">
          <div class="store-trust-icon"><?= icon('truck', 'icon') ?></div>
          <div>
            <h4 class="store-trust-title">Fast Pan-India Delivery</h4>
            <p class="store-trust-desc">Insured courier dispatch across all major Indian cities with real-time tracking.</p>
          </div>
        </div>

        <div class="store-trust-card">
          <div class="store-trust-icon"><?= icon('shield', 'icon') ?></div>
          <div>
            <h4 class="store-trust-title">1-Year Certified Warranty</h4>
            <p class="store-trust-desc">All hardware modules include official replacement warranty and direct engineer diagnostics.</p>
          </div>
        </div>

        <div class="store-trust-card">
          <div class="store-trust-icon"><?= icon('credit-card', 'icon') ?></div>
          <div>
            <h4 class="store-trust-title">256-Bit SSL Secure Payments</h4>
            <p class="store-trust-desc">Pay safely via UPI, Net Banking, Debit/Credit Cards, or Corporate Purchase Orders.</p>
          </div>
        </div>

        <div class="store-trust-card">
          <div class="store-trust-icon"><?= icon('phone', 'icon') ?></div>
          <div>
            <h4 class="store-trust-title">Bhopal Engineering Support</h4>
            <p class="store-trust-desc">Direct post-sales tech support via WhatsApp, phone, and documentation guides.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
