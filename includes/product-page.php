<?php
/**
 * Prospect Digital — shared product page template
 * ---------------------------------------------------------------------------
 * A page file in /products/ only declares its slug:
 *
 *     <?php
 *     $product_slug = 'routeflow';
 *     require __DIR__ . '/../includes/product-page.php';
 *
 * Sections: hero · quick answer · features · before/after · who it is for ·
 *           FAQ · other products · final CTA
 * ---------------------------------------------------------------------------
 */

require_once __DIR__ . '/config.php';

$product_slug = $product_slug ?? '';
$prod         = product($product_slug);

if ($prod === null) {
    http_response_code(404);
    $page_title = 'Product not found';
    require __DIR__ . '/header.php';

// Re-read this page's own record after the shared layout has been rendered:
// includes are executed in the global scope, so loop variables inside
// navbar.php / footer.php must never be able to change what this page shows.
$prod = product($product_slug) ?? $prod;

    echo '<section class="section"><div class="container container--narrow center">'
        . '<h1>That product page does not exist.</h1>'
        . '<p class="lead center-x">Browse the platforms we build, run and support.</p>'
        . '<p style="margin-top:1.5rem"><a class="btn btn--brand" href="' . e(url('products')) . '">View all products</a></p>'
        . '</div></section>';
    require __DIR__ . '/footer.php';
    exit;
}

$product_name = $prod['name'];
$demo_url     = url('contact?service=' . urlencode('Product enquiry') . '#enquiry');

$page_title       = $prod['meta_title'];
$page_description = $prod['meta_description'];
$body_class       = 'page-product page-product--' . $prod['slug'];
$hero_slug        = $prod['slug'];
$page_og_type     = 'article';

$breadcrumbs = [
    ['name' => 'Products', 'url' => 'products'],
    ['name' => $product_name, 'url' => 'products/' . $prod['slug']],
];

$page_jsonld = [[
    '@context'    => 'https://schema.org',
    '@type'       => 'SoftwareApplication',
    'name'        => $product_name . ' by ' . COMPANY_NAME,
    'applicationCategory' => 'BusinessApplication',
    'applicationSubCategory' => $prod['category'],
    'operatingSystem' => 'Web, Android, iOS',
    'description' => $prod['meta_description'],
    'url'         => absolute_url('products/' . $prod['slug']),
    'publisher'   => ['@id' => rtrim(SITE_URL, '/') . '/#organisation'],
], [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(static function (array $faq): array {
        return [
            '@type'          => 'Question',
            'name'           => $faq['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
        ];
    }, $prod['faqs']),
]];

// This page ends with its own demo CTA, so skip the shared pre-footer band.
$hide_footer_cta = true;

$cta_label       = 'REQUEST A DEMO';
$cta_title       = $prod['cta_title'];
$cta_description = $prod['cta_description'];

require __DIR__ . '/header.php';
?>

<!-- ============================ HERO ============================ -->
<section class="product-hero">
  <div class="container">
    <?php require __DIR__ . '/breadcrumbs.php'; ?>

    <div class="product-hero__inner" style="margin-top:1.5rem">
      <div>
        <div class="product-hero__brand">
          <span class="product-hero__mark" aria-hidden="true"><?= e($prod['monogram']) ?></span>
          <span>
            <span class="product-hero__cat"><?= e($prod['category']) ?></span>
            <h1 class="product-hero__name"><?= e($product_name) ?> <span class="muted" style="font-weight:500;font-size:.6em">by <?= e(COMPANY_NAME) ?></span></h1>
          </span>
        </div>

        <p class="product-hero__title" style="font-size:var(--fs-h2)"><?= e($prod['hero_title']) ?></p>
        <p class="product-hero__lead"><?= e($prod['hero_description']) ?></p>

        <div class="service-hero__actions">
          <a class="btn btn--brand btn--lg" href="<?= e($demo_url) ?>">
            Request a demo <?= icon('arrow', 'icon btn__icon') ?>
          </a>
          <a class="btn btn--outline btn--lg" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
            <?= icon('whatsapp', 'icon btn__icon') ?> WhatsApp us
          </a>
        </div>

        <div class="service-hero__meta">
          <span><?= icon('pin', 'icon') ?> Implemented from Bhopal across India</span>
          <span><?= icon('shield', 'icon') ?> Role-based access &amp; audit trails</span>
          <span><?= icon('phone', 'icon') ?> <a href="<?= e(COMPANY_PHONE_URL) ?>"><?= e(COMPANY_PHONE_DISPLAY) ?></a></span>
        </div>
      </div>

      <div class="hero-visual" data-reveal="scale">
        <div class="hero-visual__frame" style="padding:0;overflow:hidden;border-radius:14px;border:1px solid var(--glass-border);box-shadow:var(--glass-shadow)">
          <img src="<?= e(asset('images/products/' . $prod['slug'] . '.jpg')) ?>" alt="<?= e($product_name) ?> interface preview" style="width:100%;height:auto;display:block;border-radius:14px;object-fit:cover;">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================= QUICK ANSWER ========================= -->
<section class="section section--tight" aria-labelledby="productQuickTitle">
  <div class="container">
    <div class="quick-answer" data-reveal>
      <h2 class="quick-answer__title" id="productQuickTitle"><?= icon('sparkle', 'icon') ?> Quick answer</h2>
      <p class="quick-answer__text"><?= e($prod['quick_answer']) ?></p>
    </div>
  </div>
</section>

<!-- ========================== FEATURES ========================== -->
<section class="section" aria-labelledby="featuresTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">What it does</p>
      <h2 class="section-head__title" id="featuresTitle"><?= e($product_name) ?> modules at a glance.</h2>
      <p class="section-head__text">Modules are switched on as you need them, so you are not paying for functionality your team will never open.</p>
    </header>

    <div class="grid grid--3 stagger">
      <?php foreach ($prod['features'] as $feature): ?>
        <article class="feature-card" data-reveal>
          <span class="feature-card__icon"><?= icon($feature['icon'], 'icon') ?></span>
          <h3 class="feature-card__title"><?= e($feature['title']) ?></h3>
          <p class="feature-card__text"><?= e($feature['text']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ====================== BEFORE / AFTER ====================== -->
<section class="section section--soft" aria-labelledby="productImpactTitle">
  <div class="container">
    <header class="section-head section-head--center" data-reveal>
      <p class="eyebrow">BUSINESS IMPACT / <?= e(strtoupper($product_name)) ?></p>
      <h2 class="section-head__title" id="productImpactTitle">Before and after <?= e($product_name) ?>.</h2>
      <p class="section-head__text">The day-to-day difference for a business running this process on registers, spreadsheets or memory.</p>
    </header>

    <div class="compare">
      <div class="compare__col compare__col--bad" data-reveal>
        <span class="compare__tag compare__tag--bad"><?= icon('close', 'icon') ?> Before</span>
        <h3 class="compare__title"><?= e($prod['without']['title']) ?></h3>
        <ul class="compare__list">
          <?php foreach ($prod['without']['points'] as $point): ?>
            <li><?= icon('close', 'icon') ?> <span><?= e($point) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="compare__col compare__col--good" data-reveal>
        <span class="compare__tag compare__tag--good"><?= icon('check', 'icon') ?> After</span>
        <h3 class="compare__title"><?= e($prod['with']['title']) ?></h3>
        <ul class="compare__list">
          <?php foreach ($prod['with']['points'] as $point): ?>
            <li><?= icon('check', 'icon') ?> <span><?= e($point) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ======================== WHO IT IS FOR ======================== -->
<section class="section" aria-labelledby="whoForTitle">
  <div class="container grid grid--split-aside" style="display:grid">
    <div>
      <header class="section-head" data-reveal>
        <p class="eyebrow">Who it is for</p>
        <h2 class="section-head__title" id="whoForTitle">Built for teams like yours.</h2>
      </header>
      <ul class="tile-list" data-reveal>
        <?php foreach ($prod['who_for'] as $audience): ?>
          <li><?= icon('check', 'icon') ?> <span><?= e($audience) ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <aside class="form-panel" data-reveal>
      <h3 style="font-size:var(--fs-h4);margin-bottom:.4rem">Request a <?= e($product_name) ?> demo</h3>
      <p class="muted" style="font-size:var(--fs-sm);margin-bottom:1.2rem">
        Tell us about your operation and we will show the modules that fit — plus the implementation timeline.
      </p>
      <div class="chip-row">
        <a class="btn btn--brand" href="<?= e($demo_url) ?>">Open the enquiry form <?= icon('arrow', 'icon btn__icon') ?></a>
        <a class="btn btn--outline" href="<?= e(COMPANY_PHONE_URL) ?>"><?= icon('phone', 'icon btn__icon') ?> Call us</a>
      </div>
      <hr>
      <p class="eyebrow" style="margin-bottom:.6rem">Prefer WhatsApp?</p>
      <a class="arrow-link" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
        <?= icon('whatsapp', 'icon') ?> Message us directly
      </a>
    </aside>
  </div>
</section>

<!-- ============================ FAQ ============================ -->
<section class="section section--soft-2" aria-labelledby="productFaqTitle">
  <div class="container container--narrow">
    <header class="section-head section-head--center" data-reveal>
      <p class="eyebrow">Questions</p>
      <h2 class="section-head__title" id="productFaqTitle"><?= e($product_name) ?> — common questions.</h2>
    </header>

    <div class="faq" data-reveal>
      <?php foreach ($prod['faqs'] as $index => $faq): ?>
        <details class="faq__item"<?= $index === 0 ? ' open' : '' ?>>
          <summary class="faq__q"><?= e($faq['q']) ?></summary>
          <div class="faq__a"><p><?= e($faq['a']) ?></p></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======================= OTHER PRODUCTS ======================= -->
<section class="section" aria-labelledby="otherProductsTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">Explore the family</p>
      <h2 class="section-head__title" id="otherProductsTitle">Other platforms from Prospect Digital.</h2>
    </header>

    <?php
    $others = array_filter(all_products(), static fn (string $slug): bool => $slug !== $prod['slug'], ARRAY_FILTER_USE_KEY);
    $others = array_slice($others, 0, 4, true);
    ?>
    <div class="grid grid--4 stagger">
      <?php foreach ($others as $slug => $other): ?>
        <a class="product-card" href="<?= e(product_url($slug)) ?>" data-reveal>
          <span class="product-card__head">
            <span class="product-card__mark" aria-hidden="true"><?= e($other['monogram']) ?></span>
            <span>
              <span class="product-card__cat"><?= e($other['category']) ?></span>
              <h3 class="product-card__name"><?= e($other['name']) ?></h3>
            </span>
          </span>
          <p class="product-card__tagline"><?= e($other['tagline']) ?></p>
          <span class="product-card__cta">View product <?= icon('arrow', 'icon') ?></span>
        </a>
      <?php endforeach; ?>
    </div>

    <p style="margin-top:1.75rem" data-reveal>
      <a class="arrow-link" href="<?= e(url('products')) ?>">See all products <?= icon('arrow', 'icon') ?></a>
    </p>
  </div>
</section>

<!-- ========================= FINAL CTA ========================= -->
<section class="section section--tight" aria-labelledby="productCtaTitle">
  <div class="container">
    <div class="quick-answer" style="border-left-color:var(--accent);background:linear-gradient(120deg,var(--accent-tint),#FFFFFF 72%)">
      <div class="grid grid--2" style="align-items:center">
        <div data-reveal>
          <p class="eyebrow">READY FOR A DEMO?</p>
          <h2 id="productCtaTitle" style="margin-bottom:.6rem"><?= e($prod['cta_title']) ?></h2>
          <p class="muted" style="margin:0"><?= e($prod['cta_description']) ?></p>
        </div>
        <div data-reveal>
          <div class="chip-row">
            <a class="btn btn--brand btn--lg" href="<?= e($demo_url) ?>">
              Request a demo <?= icon('arrow', 'icon btn__icon') ?>
            </a>
            <a class="btn btn--outline btn--lg" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
              <?= icon('whatsapp', 'icon btn__icon') ?> WhatsApp us
            </a>
          </div>
          <p style="margin:1.1rem 0 0">
            <a class="arrow-link" href="<?= e(COMPANY_PHONE_URL) ?>"><?= icon('phone', 'icon') ?> Or call <?= e(COMPANY_PHONE_DISPLAY) ?></a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
