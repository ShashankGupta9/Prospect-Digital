<?php
/**
 * Prospect Digital — services index (all 8 service lines)
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'Services — Software, Websites, Marketing, Ads, Branding, IT, AI & Growth';
$page_description = 'Eight services under one roof: custom software development, websites, digital marketing, paid ads, branding, IT & cloud, AI & automation and growth strategy — delivered from Bhopal, India.';
$body_class       = 'page-services';
$hero_slug        = 'services-overview';

$breadcrumbs = [
    ['name' => 'Services', 'url' => 'services'],
];

// Structured data: the eight services as an ordered list.
$service_list_items = [];
foreach (array_values(all_services()) as $index => $item) {
    $service_list_items[] = [
        '@type'    => 'ListItem',
        'position' => $index + 1,
        'name'     => $item['name'],
        'url'      => absolute_url('services/' . $item['slug']),
    ];
}

$page_jsonld = [[
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => 'Prospect Digital services',
    'itemListElement' => $service_list_items,
]];

$cta_label       = 'NOT SURE WHERE TO START?';
$cta_title       = "Tell us the problem. We'll pick the right service.";
$cta_description = 'Most businesses need two or three of these services together. A short conversation is usually enough to work out the order.';

require __DIR__ . '/includes/header.php';
?>

<section class="service-hero">
  <div class="container">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>
    <div class="service-hero__inner" style="margin-top:1.5rem">
      <div>
        
        <h1 class="service-hero__title">Everything a growing business needs online — built by one team.</h1>
        <p class="service-hero__lead">From the first website to a fully automated operation: we design, build, launch and maintain the digital systems your business runs on.</p>
        <div class="service-hero__actions">
          <a class="btn btn--brand btn--lg" href="<?= e(url('contact')) ?>">
            Get a free consultation <?= icon('arrow', 'icon btn__icon') ?>
          </a>
          <a class="btn btn--outline btn--lg" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
            <?= icon('whatsapp', 'icon btn__icon') ?> WhatsApp us
          </a>
        </div>
        <div class="service-hero__meta">
          <span><?= icon('pin', 'icon') ?> Based in Bhopal, working across India</span>
          <span><?= icon('clock', 'icon') ?> <?= e(COMPANY_HOURS) ?></span>
          <span><?= icon('shield', 'icon') ?> Fixed scope before we start</span>
        </div>
      </div>
      <div class="hero-visual" data-reveal="scale">
        <div class="hero-visual__frame">
          <?= mockup('analytics', 'mockup') ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======================= SERVICE LIST ======================= -->
<section class="section" aria-labelledby="allServicesTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">All services</p>
      <h2 class="section-head__title" id="allServicesTitle">Choose a service to see the detail.</h2>
      <p class="section-head__text">Each page explains the outcome, what is delivered, the process and the business impact — so you can judge the fit before we speak.</p>
    </header>

    <div class="bento-showcase-stack">
      <?php $i = 0; foreach (all_services() as $slug => $svc): $i++; ?>
        <article class="bento-card" data-reveal>
          <div class="bento-card__grid">
            <div class="bento-card__main">
              <div class="flex-between" style="justify-content:flex-start;gap:.75rem">
                <span class="badge-num"><?= e($svc['number']) ?></span>
                <span class="pill"><?= icon($svc['icon'], 'icon') ?> <?= e($svc['nav']) ?></span>
              </div>

              <h2 class="bento-gradient-title">
                <a href="<?= e(service_url($slug)) ?>" style="color:inherit;text-decoration:none"><?= e($svc['name']) ?></a>
              </h2>

              <p style="color:var(--ink-soft);font-size:0.95rem;margin:0"><?= e($svc['card_tag']) ?></p>

              <div>
                <p class="eyebrow" style="margin-bottom:.6rem">Core Deliverables</p>
                <ul class="check-list check-list--2">
                  <?php foreach (array_slice($svc['deliverables'], 0, 4) as $item): ?>
                    <li class="check-list__item"><?= icon('check', 'icon') ?> <span><?= e($item) ?></span></li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <div class="chip-row" style="margin-top:0.5rem">
                <a class="btn btn--brand btn--sm" href="<?= e(service_url($slug)) ?>">
                  Explore Service <?= icon('arrow', 'icon btn__icon') ?>
                </a>
                <a class="btn btn--outline btn--sm" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
                  <?= icon('whatsapp', 'icon btn__icon') ?> Consult on WhatsApp
                </a>
              </div>

              <!-- Mini Bento Sub-Highlights -->
              <div class="bento-mini-grid">
                <div class="bento--mini">
                  <div class="bento--mini__icon"><?= icon($svc['icon'], 'icon') ?></div>
                  <div class="bento--mini__info">
                    <div class="bento--mini__title">Scope &amp; Architecture</div>
                    <div class="bento--mini__sub">Fixed milestones &amp; SLAs</div>
                  </div>
                </div>
                <a href="<?= e(service_url($slug)) ?>" class="bento--mini">
                  <div class="bento--mini__icon">⚡</div>
                  <div class="bento--mini__info">
                    <div class="bento--mini__title">Full Case Specs</div>
                    <div class="bento--mini__sub">View outcome &amp; ROI</div>
                  </div>
                  <span class="bento--mini__arrow">→</span>
                </a>
              </div>
            </div>

            <!-- Sticky Visual Showcase -->
            <div class="bento-card__visual-wrap">
              <span class="bento-floating-pill">✨ <?= e($svc['nav']) ?> Suite</span>
              <a href="<?= e(service_url($slug)) ?>">
                <img src="<?= e(asset('images/services/' . $slug . '.jpg')) ?>" alt="<?= e($svc['name']) ?>" loading="lazy">
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== HOW ENGAGEMENTS WORK ==================== -->
<section class="section section--soft" aria-labelledby="engageTitle">
  <div class="container">
    <header class="section-head section-head--center" data-reveal>
      <p class="eyebrow">Engagement models</p>
      <h2 class="section-head__title" id="engageTitle">Work with us the way that suits you.</h2>
      <p class="section-head__text">Project, retainer or a paid discovery — you choose the level of commitment at each stage.</p>
    </header>

    <div class="grid grid--3 stagger">
      <article class="card" data-reveal>
        <span class="card__icon"><?= icon('layers', 'icon') ?></span>
        <h3 class="card__title">Fixed-scope project</h3>
        <p class="card__text">Best for websites, software builds and brand systems. You receive a written scope, milestone plan and fixed price before work begins.</p>
        <div class="card__foot"><a class="arrow-link" href="<?= e(url('contact')) ?>">Request a proposal <?= icon('arrow', 'icon') ?></a></div>
      </article>

      <article class="card" data-reveal>
        <span class="card__icon"><?= icon('growth', 'icon') ?></span>
        <h3 class="card__title">Monthly retainer</h3>
        <p class="card__text">Best for marketing, SEO, ads and support. A defined set of deliverables each month, with reporting against agreed numbers.</p>
        <div class="card__foot"><a class="arrow-link" href="<?= e(url('services/digital-marketing')) ?>">See marketing services <?= icon('arrow', 'icon') ?></a></div>
      </article>

      <article class="card" data-reveal>
        <span class="card__icon"><?= icon('search', 'icon') ?></span>
        <h3 class="card__title">Paid discovery</h3>
        <p class="card__text">Best when the requirement is unclear. A short, paid assessment that produces a roadmap, system architecture or campaign plan you can act on.</p>
        <div class="card__foot"><a class="arrow-link" href="<?= e(url('services/growth-strategy')) ?>">Explore growth strategy <?= icon('arrow', 'icon') ?></a></div>
      </article>
    </div>
  </div>
</section>

<!-- ========================= FAQ ========================= -->
<section class="section" aria-labelledby="servicesFaqTitle">
  <div class="container container--narrow">
    <header class="section-head section-head--center" data-reveal>
      <p class="eyebrow">Questions</p>
      <h2 class="section-head__title" id="servicesFaqTitle">Before you enquire.</h2>
    </header>

    <div class="faq" data-reveal>
      <details class="faq__item" open>
        <summary class="faq__q">Can we start with only one service?</summary>
        <div class="faq__a"><p>Yes, and most clients do. Plenty of projects begin with a single website or one marketing channel, then expand once results are visible.</p></div>
      </details>
      <details class="faq__item">
        <summary class="faq__q">Do you work with clients outside Bhopal?</summary>
        <div class="faq__a"><p>Yes. Clients across India work with us remotely through scheduled calls and a shared project tracker. Local clients are welcome to meet at our M.P. Nagar office.</p></div>
      </details>
      <details class="faq__item">
        <summary class="faq__q">How quickly can work begin?</summary>
        <div class="faq__a"><p>Discovery usually happens within a week of your enquiry, and most projects can start within two to three weeks depending on the current schedule and how quickly content or approvals are available from your side.</p></div>
      </details>
      <details class="faq__item">
        <summary class="faq__q">What do we need to provide?</summary>
        <div class="faq__a"><p>Your goals, any existing brand assets or systems, access to your domain and hosting where relevant, and one decision-maker from your side. We handle the rest and tell you exactly what is needed before each stage.</p></div>
      </details>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
