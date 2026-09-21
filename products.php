<?php
/**
 * Prospect Digital — products index (RouteFlow, Workora, Bizora, Medvora, Schova)
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'Products — RouteFlow, Workora, Bizora, Medvora & Schova';
$page_description = 'Five business platforms built by Prospect Digital: RouteFlow for logistics, Workora for office and business, Bizora CRM, Medvora hospital management and Schova school management.';
$body_class       = 'page-products';
$hero_slug        = 'products-overview';

$breadcrumbs = [
    ['name' => 'Products', 'url' => 'products'],
];

// Structured data: the five platforms as an ordered list.
$product_list_items = [];
foreach (array_values(all_products()) as $index => $item) {
    $product_list_items[] = [
        '@type'    => 'ListItem',
        'position' => $index + 1,
        'name'     => $item['name'],
        'url'      => absolute_url('products/' . $item['slug']),
    ];
}

$page_jsonld = [[
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => 'Prospect Digital products',
    'itemListElement' => $product_list_items,
]];

$cta_label       = 'SEE A PLATFORM IN ACTION';
$cta_title       = 'Which platform fits your business?';
$cta_description = 'Tell us how you operate today and we will show the platform that matches — or explain what a custom build would look like.';

require __DIR__ . '/includes/header.php';
?>

<section class="product-hero">
  <div class="container">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>
    <div class="product-hero__inner" style="margin-top:1.5rem">
      <div>
        <h1 class="product-hero__title">Platforms we build, run and keep improving.</h1>
        <p class="product-hero__lead">Our own software, used by businesses across India. Every platform can be configured, extended or integrated with the systems your team already runs.</p>
        <div class="service-hero__actions">
          <a class="btn btn--brand btn--lg" href="<?= e(url('contact')) ?>">
            Book a product demo <?= icon('arrow', 'icon btn__icon') ?>
          </a>
          <a class="btn btn--outline btn--lg" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
            <?= icon('whatsapp', 'icon btn__icon') ?> Ask a question
          </a>
        </div>
      </div>
      <div class="hero-visual" data-reveal="scale">
        <div class="hero-visual__frame"><?= mockup('table', 'mockup') ?></div>
      </div>
    </div>
  </div>
</section>

<!-- ======================= PRODUCT GRID ======================= -->
<section class="section" aria-labelledby="productsTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">The product family</p>
      <h2 class="section-head__title" id="productsTitle">Purpose-built for the way Indian businesses operate.</h2>
      <p class="section-head__text">Each platform solves one sector properly instead of trying to do everything badly — and all five share the same security, backup and support standards.</p>
    </header>

    <div class="bento-showcase-stack">
      <?php foreach (all_products() as $slug => $prod): ?>
        <article class="bento-card" data-reveal>
          <div class="bento-card__grid">
            <div class="bento-card__main">
              <div class="product-hero__brand" style="margin-bottom:0">
                <span class="product-hero__mark" aria-hidden="true"><?= e($prod['monogram']) ?></span>
                <div>
                  <span class="product-hero__cat"><?= e($prod['category']) ?></span>
                  <h2 class="bento-gradient-title" style="margin-top:0.2rem">
                    <a href="<?= e(product_url($slug)) ?>" style="color:inherit;text-decoration:none"><?= e($prod['name']) ?></a>
                  </h2>
                </div>
              </div>

              <p style="color:var(--ink-soft);font-size:0.95rem;margin:0"><?= e($prod['tagline']) ?></p>

              <div>
                <p class="eyebrow" style="margin-bottom:.6rem">Key Capabilities</p>
                <ul class="check-list check-list--2">
                  <?php foreach (array_slice($prod['features'], 0, 4) as $feature): ?>
                    <li class="check-list__item"><?= icon('check', 'icon') ?> <span><?= e($feature['title']) ?></span></li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <div class="chip-row" style="margin-top:0.5rem">
                <a class="btn btn--brand btn--sm" href="<?= e(product_url($slug)) ?>">
                  Platform Specs <?= icon('arrow', 'icon btn__icon') ?>
                </a>
                <a class="btn btn--outline btn--sm" href="<?= e(url('contact?service=Product%20enquiry')) ?>">
                  Book Live Demo
                </a>
                <a class="btn btn--outline btn--sm" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
                  <?= icon('whatsapp', 'icon btn__icon') ?> WhatsApp
                </a>
              </div>

              <!-- Mini Bento Sub-Highlights -->
              <div class="bento-mini-grid">
                <div class="bento--mini">
                  <div class="bento--mini__icon">⚡</div>
                  <div class="bento--mini__info">
                    <div class="bento--mini__title">Turnkey Setup</div>
                    <div class="bento--mini__sub">Quick cloud rollout</div>
                  </div>
                </div>
                <a href="<?= e(product_url($slug)) ?>" class="bento--mini">
                  <div class="bento--mini__icon">🔒</div>
                  <div class="bento--mini__info">
                    <div class="bento--mini__title">Enterprise Security</div>
                    <div class="bento--mini__sub">Role access &amp; backups</div>
                  </div>
                  <span class="bento--mini__arrow">→</span>
                </a>
              </div>
            </div>

            <!-- Sticky Visual Showcase -->
            <div class="bento-card__visual-wrap">
              <span class="bento-floating-pill">✨ <?= e($prod['monogram']) ?> Dashboard</span>
              <a href="<?= e(product_url($slug)) ?>">
                <img src="<?= e(asset('images/products/' . $slug . '.jpg')) ?>" alt="<?= e($prod['name']) ?> AI Dashboard" loading="lazy">
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ====================== COMPARISON TABLE ====================== -->
<section class="section section--soft" aria-labelledby="compareTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">Quick comparison</p>
      <h2 class="section-head__title" id="compareTitle">Which platform is right for you?</h2>
    </header>

    <div class="table-wrap" data-reveal>
      <table class="prose" style="max-width:none;background:#fff;border-radius:var(--r-lg);overflow:hidden">
        <thead>
          <tr>
            <th scope="col">Platform</th>
            <th scope="col">Built for</th>
            <th scope="col">Core modules</th>
            <th scope="col">Typical user</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $table_rows = [
              'routeflow' => ['Transport, distribution and delivery operations', 'Routing, dispatch, driver app, proof of delivery, billing', 'Transport manager, dispatch team'],
              'workora'   => ['Office administration and teams', 'Tasks, attendance, leave, expenses, approvals, HR records', 'Owner, HR and office manager'],
              'bizora'    => ['Sales teams that follow up on leads', 'Lead capture, pipeline, reminders, quotations, reporting', 'Sales head and field team'],
              'medvora'   => ['Hospitals, clinics and diagnostic centres', 'OPD/IPD, appointments, pharmacy, lab, billing', 'Front desk, doctors, accounts'],
              'schova'    => ['Schools and coaching institutes', 'Admissions, attendance, fees, exams, transport, parents', 'Principal, office and teachers'],
          ];
          foreach ($table_rows as $slug => $row):
              $prod = product($slug);
              if (!$prod) { continue; } ?>
            <tr>
              <th scope="row"><a href="<?= e(product_url($slug)) ?>"><?= e($prod['name']) ?></a></th>
              <td><?= e($row[0]) ?></td>
              <td><?= e($row[1]) ?></td>
              <td><?= e($row[2]) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <p class="muted" style="margin-top:1.25rem;font-size:var(--fs-sm)" data-reveal>
      Not on this list? Custom software built around your exact workflow is a core service.
      <a href="<?= e(service_url('software-development')) ?>">See software development</a>.
    </p>
  </div>
</section>

<!-- ========================= FAQ ========================= -->
<section class="section" aria-labelledby="productsFaqTitle">
  <div class="container container--narrow">
    <header class="section-head section-head--center" data-reveal>
      <p class="eyebrow">Questions</p>
      <h2 class="section-head__title" id="productsFaqTitle">Licensing, hosting and support.</h2>
    </header>

    <div class="faq" data-reveal>
      <details class="faq__item" open>
        <summary class="faq__q">How is a platform licensed?</summary>
        <div class="faq__a"><p>Platforms are offered on an annual subscription that includes hosting, updates, backups and support, with user or module-based tiers. Custom-built software is quoted as a one-time project with an optional annual maintenance agreement.</p></div>
      </details>
      <details class="faq__item">
        <summary class="faq__q">Can a platform be customised for our company?</summary>
        <div class="faq__a"><p>Yes. Workflows, approval chains, document formats, roles, dashboards and integrations are all configurable, and deeper changes can be developed as part of your subscription or as a separate scope.</p></div>
      </details>
      <details class="faq__item">
        <summary class="faq__q">Where is our data stored?</summary>
        <div class="faq__a"><p>On cloud infrastructure in India by default. Clients who require it can run the platform on their own server. Data ownership always stays with you, and we document retention and backup policies in writing.</p></div>
      </details>
      <details class="faq__item">
        <summary class="faq__q">What does implementation involve?</summary>
        <div class="faq__a"><p>Configuration, data migration from your existing registers or spreadsheets, role setup, staff training and a go-live plan — usually two to six weeks depending on scale and the number of branches.</p></div>
      </details>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
