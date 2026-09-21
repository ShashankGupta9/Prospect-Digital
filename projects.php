<?php
/**
 * Prospect Digital — projects / selected work
 * ---------------------------------------------------------------------------
 * HONESTY NOTE: the engagement snapshots below are sector-level summaries.
 * Replace them with named client case studies (with written permission and
 * real, verifiable numbers) as soon as they are available. Do not add client
 * names, logos or performance figures that have not been approved in writing.
 * ---------------------------------------------------------------------------
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'Projects & Selected Work — Prospect Digital, Bhopal';
$page_description = 'Selected work by Prospect Digital: business platforms we build and run, plus sector-level engagement snapshots across manufacturing, education, healthcare, real estate, retail and professional services.';
$body_class       = 'page-projects';
$hero_slug        = 'projects';

$breadcrumbs = [
    ['name' => 'Projects', 'url' => 'projects'],
];

$page_jsonld = [[
    '@context' => 'https://schema.org',
    '@type'    => 'CollectionPage',
    'name'     => 'Projects & selected work — Prospect Digital',
    'url'      => absolute_url('projects'),
]];

$cta_label       = 'YOUR PROJECT COULD BE NEXT';
$cta_title       = "Let's discuss what you want to build.";
$cta_description = 'Tell us the outcome you need and we will map a scope, timeline and cost — with the same process described on every service page.';

require __DIR__ . '/includes/header.php';
?>

<section class="service-hero">
  <div class="container">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>
    <div class="service-hero__inner" style="margin-top:1.5rem">
      <div>
        <h1 class="service-hero__title">Software we build, run and maintain ourselves.</h1>
        <p class="service-hero__lead">Five live platforms plus client engagements across sectors. Everything listed here was designed, developed and is supported by our own team in Bhopal.</p>
        <div class="service-hero__actions">
          <a class="btn btn--brand btn--lg" href="<?= e(url('contact')) ?>">
            Discuss your project <?= icon('arrow', 'icon btn__icon') ?>
          </a>
          <a class="btn btn--outline btn--lg" href="<?= e(url('products')) ?>">Explore the platforms</a>
        </div>
      </div>
      <div class="hero-visual" data-reveal="scale">
        <div class="hero-visual__frame"><?= mockup('map', 'mockup') ?></div>
      </div>
    </div>
  </div>
</section>

<!-- ====================== OWN PLATFORMS ====================== -->
<section class="section" aria-labelledby="platformsTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">Our own products</p>
      <h2 class="section-head__title" id="platformsTitle">Platforms in active use.</h2>
      <p class="section-head__text">Building products keeps our engineering honest — we live with the systems we recommend.</p>
    </header>

    <div class="grid grid--3 stagger">
      <?php foreach ($PD_WORK['platforms'] as $slug): $plat = product($slug); if (!$plat) { continue; } ?>
        <a class="product-card" href="<?= e(product_url($slug)) ?>" data-reveal>
          <span class="product-card__head">
            <span class="product-card__mark" aria-hidden="true"><?= e($plat['monogram']) ?></span>
            <span>
              <span class="product-card__cat"><?= e($plat['category']) ?></span>
              <h3 class="product-card__name"><?= e($plat['name']) ?></h3>
            </span>
          </span>
          <p class="product-card__tagline"><?= e($plat['tagline']) ?></p>
          <span class="product-card__cta">View product <?= icon('arrow', 'icon') ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== ENGAGEMENT SNAPSHOTS ==================== -->
<section class="section section--soft" aria-labelledby="snapshotsTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">Client engagements</p>
      <h2 class="section-head__title" id="snapshotsTitle">Sector-level snapshots.</h2>
      <p class="section-head__text">
        We publish engagements by sector and scope rather than naming clients, because we do not disclose a client's operations without written permission.
        Detailed references are shared during a conversation, with the client's consent.
      </p>
    </header>

    <div class="grid grid--2 stagger">
      <?php foreach ($PD_WORK['snapshots'] as $snapshot): ?>
        <article class="card" data-reveal>
          <div class="flex-between" style="justify-content:flex-start;gap:.75rem;margin-bottom:.9rem">
            <span class="pill pill--brand"><?= icon('layers', 'icon') ?> <?= e($snapshot['sector']) ?></span>
          </div>
          <h3 class="card__title"><?= e($snapshot['scope']) ?></h3>
          <p class="card__text"><?= e($snapshot['result']) ?></p>
          <div class="card__foot">
            <p class="eyebrow" style="margin-bottom:.5rem">Services used</p>
            <div class="chip-row">
              <?php foreach ($snapshot['services'] as $slug): $s = service($slug); if (!$s) { continue; } ?>
                <a class="pill" href="<?= e(service_url($slug)) ?>"><?= e($s['nav']) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======================= WHAT WE MEASURE ======================= -->
<section class="section" aria-labelledby="measureTitle">
  <div class="container split">
    <div>
      <header class="section-head" data-reveal>
        <p class="eyebrow">How we report work</p>
        <h2 class="section-head__title" id="measureTitle">Outcomes we report against.</h2>
        <p class="section-head__text">Every project starts with the numbers that will prove it worked. These are the measures we use most often.</p>
      </header>

      <ul class="check-list check-list--brand" style="--stack-gap:1.15rem" data-reveal>
        <li class="check-list__item"><?= icon('check', 'icon') ?> <span><strong>Time saved</strong> — hours per week returned to your team by software or automation.</span></li>
        <li class="check-list__item"><?= icon('check', 'icon') ?> <span><strong>Enquiry volume and cost</strong> — leads tracked by channel with cost per enquiry.</span></li>
        <li class="check-list__item"><?= icon('check', 'icon') ?> <span><strong>Process accuracy</strong> — duplicate entries, billing corrections and stock variance reduced.</span></li>
        <li class="check-list__item"><?= icon('check', 'icon') ?> <span><strong>System reliability</strong> — uptime, backup verification and response times on support.</span></li>
      </ul>
    </div>

    <div class="contact-card" data-reveal>
      <h3 style="font-size:var(--fs-h4)">Want references before you commit?</h3>
      <p class="muted" style="font-size:var(--fs-sm)">
        Ask us on the call. With client permission we arrange a direct conversation with a business in a similar sector and scale, so you can hear the experience first-hand rather than reading a testimonial we wrote ourselves.
      </p>
      <div class="chip-row" style="margin-top:1.25rem">
        <a class="btn btn--brand btn--sm" href="<?= e(url('contact')) ?>">Request a reference</a>
        <a class="btn btn--outline btn--sm" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
          <?= icon('whatsapp', 'icon btn__icon') ?> WhatsApp
        </a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
