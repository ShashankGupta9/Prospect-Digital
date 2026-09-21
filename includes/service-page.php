<?php
/**
 * Prospect Digital — Shared Service Page Template (Modern SaaS Template Layout)
 */
require_once __DIR__ . '/config.php';

$service_slug = $service_slug ?? '';
$svc          = service($service_slug);

if ($svc === null) {
    http_response_code(404);
    $page_title = 'Service not found';
    require __DIR__ . '/header.php';
    echo '<section class="section"><div class="container container--narrow center">'
        . '<h1>That service page does not exist.</h1>'
        . '<p class="lead center-x">Please choose one of our services from the list below.</p>'
        . '<p style="margin-top:1.5rem"><a class="btn btn--brand" href="' . e(url('services')) . '">View all services</a></p>'
        . '</div></section>';
    require __DIR__ . '/footer.php';
    exit;
}

$service_name  = $svc['name'];
$service_short = $svc['nav'];
$enquiry_url   = url('contact?service=' . urlencode($service_name) . '#enquiry');

$page_title       = $svc['meta_title'];
$page_description = $svc['meta_description'];
$body_class       = 'page-service page-service--' . $svc['slug'];
$hero_slug        = $svc['slug'];
$page_og_type     = 'article';

$breadcrumbs = [
    ['name' => 'Services', 'url' => 'services'],
    ['name' => $service_short, 'url' => 'services/' . $svc['slug']],
];

$page_jsonld = [[
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    'name'        => $service_name,
    'serviceType' => $service_name,
    'description' => $svc['meta_description'],
    'url'         => absolute_url('services/' . $svc['slug']),
    'provider'    => ['@id' => rtrim(SITE_URL, '/') . '/#organisation'],
]];

require __DIR__ . '/header.php';
?>

<!-- ============================ 1. SERVICE HERO ============================ -->
<section class="hero-tech" id="service-hero" aria-labelledby="serviceTitle">
  <div class="hero-circuit-bg" aria-hidden="true"></div>

  <div class="container">
    <?php require __DIR__ . '/breadcrumbs.php'; ?>

    <div class="hero-tech__grid" style="margin-top:1.5rem">
      <!-- Left Copy -->
      <div class="hero-tech__copy" data-reveal>
        <div class="hero-tech__eyebrow-row">
          <span class="hero-tech__tag">/ <?= e(strtoupper($service_name)) ?></span>
          <span class="hero-tech__counter"><?= e($svc['number']) ?> / 08</span>
        </div>

        <h1 class="hero-tech__title" id="serviceTitle">
          <?= e($svc['hero_title']) ?>
        </h1>

        <p class="hero-tech__lead">
          <?= e($svc['hero_description']) ?>
        </p>

        <div class="hero-tech__actions">
          <a class="btn btn--brand btn--lg" href="<?= e($enquiry_url) ?>">
            Get a Free Consultation <?= icon('arrow', 'icon btn__icon') ?>
          </a>
          <a class="btn btn--phone-pill btn--lg" href="<?= e(COMPANY_PHONE_URL) ?>">
            <?= icon('phone', 'icon') ?> <?= e(COMPANY_PHONE_DISPLAY) ?>
          </a>
        </div>

        <div class="hero-tech__badges">
          <span class="hero-tech__badge-item">Custom Solutions</span>
          <span class="hero-tech__badge-item">Bhopal &amp; India</span>
          <span class="hero-tech__badge-item">Scalable &amp; Secure</span>
        </div>
      </div>

      <!-- Right 3D Visual Mockup with AI Service Image -->
      <div class="dashboard-showcase" data-reveal="scale">
        <div style="border-radius:18px;overflow:hidden;border:1px solid var(--glass-border);box-shadow:var(--glass-shadow)">
          <img src="<?= e(asset('images/services/' . $svc['slug'] . '.jpg')) ?>" alt="<?= e($service_name) ?> AI Interface" style="width:100%;height:auto;display:block;border-radius:18px;object-fit:cover;">
        </div>

        <!-- Floating Tag -->
        <div class="floating-workflow-tag">
          <div class="floating-workflow-tag__icon">⚡</div>
          <div>
            <div class="floating-workflow-tag__title"><?= e($service_name) ?></div>
            <div class="floating-workflow-tag__text">Built right, tested thoroughly &amp; deployed.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======================== 2. FLOATING "OUR SERVICES" TAB BAR ======================== -->
<div class="container services-bar-wrap">
  <div class="services-bar" data-reveal>
    <span class="services-bar__label">Our Services</span>
    <?php foreach (all_services() as $nav_slug => $nav_svc): ?>
      <a href="<?= e(service_url($nav_slug)) ?>" class="services-bar__tab<?= $nav_slug === $svc['slug'] ? ' is-active' : '' ?>">
        <?= icon($nav_svc['icon'], 'icon') ?> <span><?= e($nav_svc['nav']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- ==================== 3. OUTCOME & QUICK ANSWER SHOWCASE ==================== -->
<section class="section" id="service-quick-answer" aria-labelledby="serviceQuickAnswerTitle">
  <div class="container">
    <div class="showcase-grid">
      <!-- Left Card: Quick Answer -->
      <article class="showcase-card" data-reveal>
        <div class="code-orb-badge" aria-hidden="true">
          &lt;/&gt;
        </div>
        <p class="eyebrow">QUICK ANSWER</p>
        <h2 class="showcase-card__title" id="serviceQuickAnswerTitle">
          <?= e($svc['quick_answer']) ?>
        </h2>
      </article>

      <!-- Right Card: The Outcome -->
      <article class="showcase-card" data-reveal>
        <p class="eyebrow"><?= e($svc['outcome_label']) ?></p>
        <h2 class="showcase-card__title">
          <?= e($svc['outcome_title']) ?>
        </h2>
        <p class="showcase-card__text">
          <?= e($svc['outcome_description']) ?>
        </p>

        <div class="outcome-features-grid">
          <?php foreach (array_slice($svc['deliverables'], 0, 6) as $item): ?>
            <div class="outcome-feature-item"><?= icon('check', 'icon') ?> <?= e($item) ?></div>
          <?php endforeach; ?>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ==================== 4. BEFORE VS AFTER ==================== -->
<section class="section section--soft" id="service-impact" aria-labelledby="serviceImpactTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">BEFORE VS AFTER</p>
      <h2 class="section-head__title" id="serviceImpactTitle">From chaos to control.</h2>
      <p class="lead">
        What changes for your business when <?= e(strtolower($service_name)) ?> is done properly.
      </p>
    </header>

    <div class="compare-wrapper" data-reveal>
      <div class="compare-container">
        <!-- Without Box -->
        <div class="compare-box compare-box--without">
          <div>
            <div class="compare-box__head">WITHOUT THIS SERVICE</div>
            <ul class="compare-list">
              <?php foreach ($svc['without']['points'] as $point): ?>
                <li class="compare-list__item compare-list__item--bad">
                  <span class="icon">✕</span> <span><?= e($point) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <div style="background:#fff;border:1px solid #fee2e2;border-radius:8px;padding:1rem;display:flex;align-items:center;gap:10px">
            <span style="font-size:1.4rem">⚠️</span>
            <span style="font-size:0.8rem;color:#991b1b;font-weight:600"><?= e($svc['without']['title']) ?></span>
          </div>
        </div>

        <!-- Center Arrow -->
        <div class="compare-arrow-btn" aria-hidden="true">
          →
        </div>

        <!-- With Box -->
        <div class="compare-box compare-box--with">
          <div>
            <div class="compare-box__head">WITH PROSPECT DIGITAL</div>
            <ul class="compare-list">
              <?php foreach ($svc['with']['points'] as $point): ?>
                <li class="compare-list__item compare-list__item--good">
                  <span class="icon">✓</span> <span><?= e($point) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <div style="background:#fff;border:1px solid #d1fae5;border-radius:8px;padding:1rem;display:flex;align-items:center;gap:10px">
            <span style="font-size:1.4rem">✨</span>
            <span style="font-size:0.8rem;color:#065f46;font-weight:600"><?= e($svc['with']['title']) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==================== 5. WHAT WE DELIVER ==================== -->
<section class="section" id="service-deliverables" aria-labelledby="serviceDeliverablesTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">WHAT WE DELIVER</p>
      <h2 class="section-head__title" id="serviceDeliverablesTitle">
        Complete solutions for every stage of your business.
      </h2>
    </header>

    <div class="grid grid--6 stagger" style="margin-top:1.5rem">
      <?php foreach ($svc['deliverables'] as $item): ?>
        <div class="deliverable-card" data-reveal>
          <div class="deliverable-card__icon"><?= icon('check', 'icon') ?></div>
          <h3 class="deliverable-card__title"><?= e($item) ?></h3>
          <p class="deliverable-card__text">Tailored, documented &amp; fully supported.</p>
          <span class="deliverable-card__arrow">→</span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== 6. OUR PROCESS ==================== -->
<section class="section section--soft" id="service-process" aria-labelledby="serviceProcessTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">OUR PROCESS</p>
      <h2 class="section-head__title" id="serviceProcessTitle">
        A clear path from idea to <span class="accent-cyan">impact.</span>
      </h2>
      <p class="lead">
        The same disciplined process runs on every engagement, whatever the size.
      </p>
    </header>

    <div class="process-timeline" data-reveal>
      <?php foreach ($svc['process'] as $step): ?>
        <div class="process-step">
          <div class="process-step__node"><?= e($step['number']) ?></div>
          <h3 class="process-step__title"><?= e($step['title']) ?></h3>
          <p class="process-step__text"><?= e($step['description']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== 7. FAQS ==================== -->
<section class="section" id="service-faq" aria-labelledby="serviceFaqTitle">
  <div class="container container--narrow">
    <header class="section-head section-head--center" data-reveal>
      <p class="eyebrow">QUESTIONS</p>
      <h2 class="section-head__title" id="serviceFaqTitle"><?= e($service_short) ?> — common questions.</h2>
    </header>

    <div class="faq" data-reveal>
      <?php foreach ($svc['faqs'] as $index => $faq): ?>
        <details class="faq__item"<?= $index === 0 ? ' open' : '' ?>>
          <summary class="faq__q"><?= e($faq['q']) ?></summary>
          <div class="faq__a"><p><?= e($faq['a']) ?></p></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
