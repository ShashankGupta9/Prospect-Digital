<?php
/**
 * Prospect Digital — 404 page
 * Served by Apache when ErrorDocument is configured (see README → Deployment),
 * or requested directly.
 */
require_once __DIR__ . '/includes/config.php';

http_response_code(404);

$page_title       = 'Page not found — Prospect Digital';
$page_description = 'The page you were looking for does not exist or has moved. Use the links below to find what you need.';
$page_robots      = 'noindex, follow';
$body_class       = 'page-404';
$hide_footer_cta  = true;

require __DIR__ . '/includes/header.php';
?>

<section class="section section--lg">
  <div class="container container--narrow center">
    <p class="eyebrow center-x">ERROR 404</p>
    <h1 style="font-size:var(--fs-display)">That page has moved or never existed.</h1>
    <p class="lead center-x">
      No harm done. Pick a destination below, or tell us what you were looking for and we will point you to it.
    </p>

    <div class="chip-row" style="justify-content:center;margin-top:1.75rem">
      <a class="btn btn--brand btn--lg" href="<?= e(url('')) ?>">Back to home <?= icon('arrow', 'icon btn__icon') ?></a>
      <a class="btn btn--outline btn--lg" href="<?= e(url('services')) ?>">Browse services</a>
      <a class="btn btn--outline btn--lg" href="<?= e(url('contact')) ?>">Contact us</a>
    </div>

    <div class="link-grid" style="margin-top:clamp(2.5rem,6vw,4rem);text-align:left">
      <div class="link-grid__col">
        <h2 class="link-grid__title">Popular services</h2>
        <ul class="link-grid__list">
          <?php foreach (['software-development', 'website-development', 'digital-marketing', 'it-services-cloud'] as $slug): $svc = service($slug); if (!$svc) { continue; } ?>
            <li><a href="<?= e(service_url($slug)) ?>"><?= e($svc['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="link-grid__col">
        <h2 class="link-grid__title">Products</h2>
        <ul class="link-grid__list">
          <?php foreach (all_products() as $slug => $prod): ?>
            <li><a href="<?= e(product_url($slug)) ?>"><?= e($prod['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="link-grid__col">
        <h2 class="link-grid__title">Company</h2>
        <ul class="link-grid__list">
          <li><a href="<?= e(url('about')) ?>">About</a></li>
          <li><a href="<?= e(url('projects')) ?>">Projects</a></li>
          <li><a href="<?= e(url('guides')) ?>">Guides</a></li>
          <li><a href="<?= e(url('sitemap')) ?>">Full sitemap</a></li>
        </ul>
      </div>
    </div>

    <p class="muted" style="margin-top:2rem;font-size:var(--fs-sm)">
      Still stuck? Call <a href="<?= e(COMPANY_PHONE_URL) ?>"><?= e(COMPANY_PHONE_DISPLAY) ?></a> or
      <a href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">message us on WhatsApp</a>.
    </p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
