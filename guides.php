<?php
/**
 * Prospect Digital — guides index
 * ---------------------------------------------------------------------------
 * Short, practical buyer guides. Each guide links through to the service page
 * that covers the topic in detail, so there is one authoritative place for
 * every subject instead of duplicated content.
 * ---------------------------------------------------------------------------
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'Guides — Practical Buying Advice for Software, Websites & Marketing';
$page_description = 'Short guides from Prospect Digital on choosing software, planning a business website, local SEO, judging ad reports, preparing IT setup and choosing what to automate first.';
$body_class       = 'page-guides';
$hero_slug        = 'guides';

$breadcrumbs = [
    ['name' => 'Guides', 'url' => 'guides'],
];

$cta_label       = 'WANT ADVICE ON YOUR CASE?';
$cta_title       = 'Guides are general — your situation is not.';
$cta_description = 'Describe your requirement and we will give a specific recommendation instead of a general one.';

require __DIR__ . '/includes/header.php';
?>

<section class="service-hero">
  <div class="container">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>
    <div class="service-hero__inner" style="margin-top:1.5rem">
      <div>
        <h1 class="service-hero__title">Straight answers before you spend money.</h1>
        <p class="service-hero__lead">Each guide is a short summary of the questions we get asked most often, written for business owners rather than developers.</p>
        <div class="service-hero__actions">
          <a class="btn btn--brand btn--lg" href="<?= e(url('contact')) ?>">
            Ask a specific question <?= icon('arrow', 'icon btn__icon') ?>
          </a>
          <a class="btn btn--outline btn--lg" href="<?= e(url('services')) ?>">Browse services</a>
        </div>
      </div>
      <div class="hero-visual" data-reveal="scale">
        <div class="hero-visual__frame"><?= mockup('website', 'mockup') ?></div>
      </div>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="guidesTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">All guides</p>
      <h2 class="section-head__title" id="guidesTitle">Six questions we hear every week.</h2>
      <p class="section-head__text">Pick the topic closest to your situation — the linked service page has the full detail.</p>
    </header>

    <div class="grid grid--3 stagger">
      <?php foreach ($PD_GUIDES as $guide): $svc = service($guide['read']); ?>
        <a class="guide-card" href="<?= e(service_url($guide['read'])) ?>" data-reveal>
          <span class="pill pill--brand"><?= e($guide['tag']) ?></span>
          <h3 class="guide-card__title"><?= e($guide['title']) ?></h3>
          <p class="guide-card__text"><?= e($guide['text']) ?></p>
          <span class="guide-card__cta">
            Read in the <?= e($svc['nav'] ?? 'service') ?> guide <?= icon('arrow', 'icon') ?>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--soft" aria-labelledby="guidesFaqTitle">
  <div class="container container--narrow">
    <header class="section-head section-head--center" data-reveal>
      <p class="eyebrow">Keep it simple</p>
      <h2 class="section-head__title" id="guidesFaqTitle">Three rules that save most projects.</h2>
    </header>

    <div class="faq" data-reveal>
      <details class="faq__item" open>
        <summary class="faq__q">Start with the process, not the software.</summary>
        <div class="faq__a"><p>Write down how the work actually happens today — who does what, in which order, and where it slows down. Software that ignores this step becomes another thing to work around.</p></div>
      </details>
      <details class="faq__item">
        <summary class="faq__q">Buy the version one step ahead of where you are.</summary>
        <div class="faq__a"><p>Systems that try to solve problems you do not have yet are expensive and get abandoned. Choose the smallest build that fixes today's bottleneck properly, then extend when the next one appears.</p></div>
      </details>
      <details class="faq__item">
        <summary class="faq__q">Agree on one number that proves it worked.</summary>
        <div class="faq__a"><p>Enquiries per month, hours saved per week, error rate, cost per lead — pick one before work starts. Without it, every project ends in an opinion instead of a result.</p></div>
      </details>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
