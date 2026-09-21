<?php
/**
 * Prospect Digital — human-readable sitemap
 * The XML version for search engines is /sitemap.xml (served by sitemap.xml.php).
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'Sitemap — Every Page on the Prospect Digital Website';
$page_description = 'A complete list of pages on the Prospect Digital website: services, products, company pages, legal pages and the XML sitemap for search engines.';
$body_class       = 'page-sitemap';

$breadcrumbs = [
    ['name' => 'Sitemap', 'url' => 'sitemap'],
];

require __DIR__ . '/includes/header.php';
?>

<section class="service-hero" style="padding-bottom:clamp(1.5rem,4vw,2.5rem)">
  <div class="container">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>
    <h1 style="margin-top:1.25rem">Sitemap</h1>
    <p class="service-hero__lead">Every public page on this website, in one place. The machine-readable version for search engines is at
      <a href="<?= e(url('sitemap.xml')) ?>">/sitemap.xml</a>.</p>
  </div>
</section>

<section class="section" style="padding-top:clamp(1.5rem,4vw,2.5rem)">
  <div class="container">
    <div class="link-grid" data-reveal>
      <div class="link-grid__col">
        <h2 class="link-grid__title">Company</h2>
        <ul class="link-grid__list">
          <li><a href="<?= e(url('')) ?>"><img src="https://imgs.search.brave.com/tQAEn_8B48EA5e89sPa_z4R8ZnYk-t8COwQhS8-MnTg/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zdGF0/aWMudmVjdGVlenku/Y29tL3N5c3RlbS9y/ZXNvdXJjZXMvdGh1/bWJuYWlscy8wMDgv/MTIyLzkzOS9zbWFs/bC9ob21lLWZvci13/ZWJzaXRlLXN5bWJv/bC1pY29uLXByZXNl/bnRhdGlvbi1mcmVl/LXZlY3Rvci5qcGc" ></a></li>
          <li><a href="<?= e(url('about')) ?>">About</a></li>
          <li><a href="<?= e(url('services')) ?>">Services overview</a></li>
          <li><a href="<?= e(url('products')) ?>">Products overview</a></li>
          <li><a href="<?= e(url('projects')) ?>">Projects</a></li>
          <li><a href="<?= e(url('guides')) ?>">Guides</a></li>
          <li><a href="<?= e(url('contact')) ?>">Contact</a></li>
        </ul>
      </div>

      <div class="link-grid__col">
        <h2 class="link-grid__title">Services</h2>
        <ul class="link-grid__list">
          <?php foreach (all_services() as $slug => $svc): ?>
            <li><a href="<?= e(service_url($slug)) ?>"><?= e($svc['number']) ?> — <?= e($svc['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="link-grid__col">
        <h2 class="link-grid__title">Products</h2>
        <ul class="link-grid__list">
          <?php foreach (all_products() as $slug => $prod): ?>
            <li><a href="<?= e(product_url($slug)) ?>"><?= e($prod['name']) ?> — <?= e($prod['category']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="link-grid__col">
        <h2 class="link-grid__title">Legal &amp; utility</h2>
        <ul class="link-grid__list">
          <li><a href="<?= e(url('privacy')) ?>">Privacy Policy</a></li>
          <li><a href="<?= e(url('terms')) ?>">Terms of Service</a></li>
          <li><a href="<?= e(url('sitemap')) ?>">Sitemap (this page)</a></li>
          <li><a href="<?= e(url('sitemap.xml')) ?>">XML sitemap</a></li>
          <li><a href="<?= e(url('robots.txt')) ?>">robots.txt</a></li>
        </ul>
      </div>

      <div class="link-grid__col">
        <h2 class="link-grid__title">Direct actions</h2>
        <ul class="link-grid__list">
          <li><a href="<?= e(COMPANY_PHONE_URL) ?>">Call <?= e(COMPANY_PHONE_DISPLAY) ?></a></li>
          <li><a href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></li>
          <li><a href="<?= e(COMPANY_EMAIL_URL) ?>">E-mail <?= e(COMPANY_EMAIL) ?></a></li>
          <li><a href="<?= e(url('contact')) ?>#map">Directions to the Bhopal office</a></li>
        </ul>
      </div>

      <div class="link-grid__col">
        <h2 class="link-grid__title">Contact</h2>
        <p class="muted" style="font-size:var(--fs-sm)">
          <?= e(COMPANY_ADDRESS_LINE_1) ?><br>
          <?= e(COMPANY_ADDRESS_LINE_2) ?><br>
          <?= e(COMPANY_ADDRESS_LINE_3) ?><br>
          <?= e(COMPANY_ADDRESS_LINE_4) ?><br>
          <span style="display:block;margin-top:.5rem"><?= e(COMPANY_HOURS) ?></span>
        </p>
      </div>
    </div>

    <p class="muted" style="margin-top:1.75rem;font-size:var(--fs-sm)">
      Found a broken link? Please
      <a href="<?= e(url('contact')) ?>">tell us</a> and we will fix it.
    </p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
