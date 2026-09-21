<?php
/**
 * Prospect Digital — about page
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'About Prospect Digital — Digital Solutions Team in Bhopal, India';
$page_description = 'Prospect Digital is a Bhopal-based team of developers, designers and marketers building software, websites, cloud setups and growth systems for businesses across India.';
$body_class       = 'page-about';
$hero_slug        = 'about-team';

$breadcrumbs = [
    ['name' => 'About', 'url' => 'about'],
];

$page_jsonld = [[
    '@context' => 'https://schema.org',
    '@type'    => 'AboutPage',
    'name'     => 'About Prospect Digital',
    'url'      => absolute_url('about'),
    'about'    => ['@id' => rtrim(SITE_URL, '/') . '/#organisation'],
]];

$cta_label       = 'WORK WITH US';
$cta_title       = "Let's talk about your business.";
$cta_description = 'Whether you need a website, a system, cloud support or a growth plan — the first conversation is free and specific.';

require __DIR__ . '/includes/header.php';
?>

<section class="service-hero">
  <div class="container">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>
    <div class="service-hero__inner" style="margin-top:1.5rem">
      <div>
        <h1 class="service-hero__title"><?= e($PD_ABOUT['hero']['title']) ?></h1>
        <p class="service-hero__lead"><?= e($PD_ABOUT['hero']['description']) ?></p>
        <div class="service-hero__actions">
          <a class="btn btn--brand btn--lg" href="<?= e(url('contact')) ?>">
            Start a conversation <?= icon('arrow', 'icon btn__icon') ?>
          </a>
          <a class="btn btn--outline btn--lg" href="<?= e(url('projects')) ?>">See our work</a>
        </div>
      </div>
      <div class="hero-visual" data-reveal="scale">
        <div class="hero-visual__frame"><?= mockup('kanban', 'mockup') ?></div>
      </div>
    </div>
  </div>
</section>

<!-- ========================== STORY ========================== -->
<section class="section" aria-labelledby="storyTitle">
  <div class="container split">
    <div>
      <header class="section-head" data-reveal>
        <p class="eyebrow">Our approach</p>
        <h2 class="section-head__title" id="storyTitle">Technology that fits the business — not the reverse.</h2>
      </header>
      <div class="prose" data-reveal>
        <?php foreach ($PD_ABOUT['story'] as $paragraph): ?>
          <p><?= e($paragraph) ?></p>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="split__visual">
      <ul class="tile-list" data-reveal>
        <?php foreach ($PD_ABOUT['offices'] as $item): ?>
          <li>
            <?= icon('pin', 'icon') ?>
            <span>
              <strong><?= e($item['label']) ?></strong><br>
              <span class="muted"><?= e($item['value']) ?></span>
            </span>
          </li>
        <?php endforeach; ?>
        <li>
          <?= icon('mail', 'icon') ?>
          <span>
            <strong>E-mail</strong><br>
            <a href="<?= e(COMPANY_EMAIL_URL) ?>"><?= e(COMPANY_EMAIL) ?></a>
          </span>
        </li>
        <li>
          <?= icon('phone', 'icon') ?>
          <span>
            <strong>Phone</strong><br>
            <a href="<?= e(COMPANY_PHONE_URL) ?>"><?= e(COMPANY_PHONE_DISPLAY) ?></a>
          </span>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- ========================== VALUES ========================== -->
<section class="section section--soft" aria-labelledby="valuesTitle">
  <div class="container">
    <header class="section-head section-head--center" data-reveal>
      <p class="eyebrow">How to work</p>
      <h2 class="section-head__title" id="valuesTitle">Six principles we do not negotiate on.</h2>
      <p class="section-head__text">They sound simple written down. Holding to them on a difficult project is the actual work.</p>
    </header>

    <div class="grid grid--3 stagger">
      <?php foreach ($PD_ABOUT['values'] as $value): ?>
        <article class="card" data-reveal>
          <span class="card__icon"><?= icon($value['icon'], 'icon') ?></span>
          <h3 class="card__title"><?= e($value['title']) ?></h3>
          <p class="card__text"><?= e($value['text']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======================= CAPABILITIES ======================= -->
<section class="section" aria-labelledby="capabilitiesTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">Capabilities</p>
      <h2 class="section-head__title" id="capabilitiesTitle">What the team actually does, day to day.</h2>
    </header>

    <div class="strip" data-reveal>
      <?php
      $capability_items = [
          ['icon' => 'code',    'label' => 'Application & platform engineering'],
          ['icon' => 'layers',  'label' => 'Database & system architecture'],
          ['icon' => 'globe',   'label' => 'Front-end & responsive design'],
          ['icon' => 'palette', 'label' => 'Brand, UI and creative design'],
          ['icon' => 'search',  'label' => 'SEO, content & analytics'],
          ['icon' => 'target',  'label' => 'Paid media & conversion tracking'],
          ['icon' => 'cloud',   'label' => 'Cloud, hosting & security'],
          ['icon' => 'cpu',     'label' => 'Automation & AI integration'],
      ];
      foreach ($capability_items as $item): ?>
        <div class="strip__item"><?= icon($item['icon'], 'icon') ?> <span><?= e($item['label']) ?></span></div>
      <?php endforeach; ?>
    </div>

    <div class="grid grid--3 stagger" style="margin-top:clamp(2rem,4vw,3rem)">
      <?php foreach (array_slice(all_products(), 0, 3) as $slug => $prod): ?>
        <a class="product-card" href="<?= e(product_url($slug)) ?>" data-reveal>
          <span class="product-card__head">
            <span class="product-card__mark" aria-hidden="true"><?= e($prod['monogram']) ?></span>
            <span>
              <span class="product-card__cat">Platform</span>
              <h3 class="product-card__name"><?= e($prod['name']) ?></h3>
            </span>
          </span>
          <p class="product-card__tagline"><?= e($prod['tagline']) ?></p>
          <span class="product-card__cta">Explore <?= icon('arrow', 'icon') ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:1.5rem" data-reveal>
      <a class="arrow-link" href="<?= e(url('products')) ?>">See all five platforms <?= icon('arrow', 'icon') ?></a>
    </p>
  </div>
</section>

<!-- ======================= SERVICE AREA ======================= -->
<section class="section section--soft-2" aria-labelledby="areaTitle">
  <div class="container split split--reverse">
    <div>
      <header class="section-head" data-reveal>
        <p class="eyebrow">Where we work</p>
        <h2 class="section-head__title" id="areaTitle">Rooted in Bhopal, working across India.</h2>
        <p class="section-head__text">Our head office is in M.P. Nagar, Bhopal. We meet clients locally, and run remote engagements with businesses in Indore, Jabalpur, Delhi NCR, Mumbai, Bengaluru and beyond.</p>
      </header>

      <ul class="check-list check-list--brand" style="--stack-gap:1.25rem" data-reveal>
        <li class="check-list__item"><?= icon('check', 'icon') ?> <span><strong>Local presence</strong> — on-site visits across Bhopal for networking, hardware and office IT work.</span></li>
        <li class="check-list__item"><?= icon('check', 'icon') ?> <span><strong>Remote-first delivery</strong> — scheduled calls, shared trackers and written updates for out-of-city projects.</span></li>
        <li class="check-list__item"><?= icon('check', 'icon') ?> <span><strong>One point of contact</strong> — a named team member accountable for your project from scope to support.</span></li>
      </ul>
    </div>

    <div class="contact-card" data-reveal>
      <h3>Visit or call us</h3>
      <address class="muted" style="line-height:1.75">
        <?= e(COMPANY_ADDRESS_LINE_1) ?><br>
        <?= e(COMPANY_ADDRESS_LINE_2) ?><br>
        <?= e(COMPANY_ADDRESS_LINE_3) ?><br>
        <?= e(COMPANY_ADDRESS_LINE_4) ?>
      </address>
      <ul class="contact-list" style="margin-top:1rem">
        <li>
          <a href="<?= e(COMPANY_PHONE_URL) ?>">
            <span class="contact-list__icon"><?= icon('phone', 'icon') ?></span>
            <span>
              <span class="contact-list__label">Phone</span>
              <span class="contact-list__value"><?= e(COMPANY_PHONE_DISPLAY) ?></span>
            </span>
          </a>
        </li>
        <li>
          <a href="<?= e(COMPANY_EMAIL_URL) ?>">
            <span class="contact-list__icon"><?= icon('mail', 'icon') ?></span>
            <span>
              <span class="contact-list__label">E-mail</span>
              <span class="contact-list__value"><?= e(COMPANY_EMAIL) ?></span>
            </span>
          </a>
        </li>
        <li>
          <div class="contact-list__row">
            <span class="contact-list__icon"><?= icon('clock', 'icon') ?></span>
            <span>
              <span class="contact-list__label">Working hours</span>
              <span class="contact-list__value"><?= e(COMPANY_HOURS) ?></span>
            </span>
          </div>
        </li>
      </ul>
      <div class="chip-row" style="margin-top:1.25rem">
        <a class="btn btn--brand btn--sm" href="<?= e(url('contact')) ?>">Send an enquiry</a>
        <a class="btn btn--outline btn--sm" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
          <?= icon('whatsapp', 'icon btn__icon') ?> WhatsApp
        </a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
