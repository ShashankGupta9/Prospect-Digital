<?php
/**
 * Prospect Digital — Homepage (Modern SaaS Template Layout)
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'Digital Solutions Company in Bhopal — Custom Software, Web & Cloud';
$page_description = 'Prospect Digital builds digital solutions that deliver business growth — custom software, ERP, CRM, websites, cloud and marketing for businesses in Bhopal and across India.';
$page_keywords    = 'software development Bhopal, custom software India, ERP CRM software, website development, Prospect Digital';
$body_class       = 'page-home';
$hero_slug        = 'home';

$page_jsonld = [[
    '@context'   => 'https://schema.org',
    '@type'      => 'WebSite',
    'name'       => COMPANY_NAME,
    'url'        => rtrim(SITE_URL, '/') . '/',
    'description'=> COMPANY_DESCRIPTION,
    'inLanguage' => 'en-IN',
    'publisher'  => ['@id' => rtrim(SITE_URL, '/') . '/#organisation'],
]];

require __DIR__ . '/includes/header.php';
?>

<!-- ============================ 1. HERO SECTION ============================ -->
<section class="hero-tech" id="hero" aria-labelledby="heroTitle">
  <div class="hero-circuit-bg" aria-hidden="true"></div>

  <div class="container">
    <div class="hero-tech__grid">
      <!-- Left Copy -->
      <div class="hero-tech__copy" data-reveal>
        <div class="hero-tech__eyebrow-row">
          <span class="hero-tech__tag">/ SOFTWARE DEVELOPMENT</span>
        </div>

        <h1 class="hero-tech__title" id="heroTitle">
          Software built around your business.
          <span class="accent-red">Not the other way around.</span>
        </h1>

        <p class="hero-tech__lead">
          We build custom software solutions — ERP, CRM, billing, inventory, customer portals and more — to help you streamline operations, reduce manual work and scale faster.
        </p>

        <div class="hero-tech__actions">
          <a class="btn btn--brand btn--lg" href="<?= e(url('contact')) ?>">
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

      <!-- Right 3D Dashboard Showcase Mockup -->
      <div class="dashboard-showcase" data-reveal="scale">
  <img src="https://images.unsplash.com/photo-1556745753-b2904692b3cd?q=80&w=1973&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Dashboard" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg, 12px);">
</div>
    </div>
  </div>
</section>

<!-- ======================== 2. FLOATING "OUR SERVICES" TAB BAR ======================== -->
<div class="container services-bar-wrap">
  <div class="services-bar" data-reveal>
   
    <a href="<?= e(service_url('software-development')) ?>" class="services-bar__tab is-active">
      <?= icon('code', 'icon') ?> <span>Development</span>
    </a>
    <a href="<?= e(service_url('website-development')) ?>" class="services-bar__tab">
      <?= icon('globe', 'icon') ?> <span>Websites</span>
    </a>
    <a href="<?= e(service_url('digital-marketing')) ?>" class="services-bar__tab">
      <?= icon('target', 'icon') ?> <span>Marketing</span>
    </a>
    <a href="<?= e(service_url('performance-marketing')) ?>" class="services-bar__tab">
      <?= icon('chart', 'icon') ?> <span>Paid Ads</span>
    </a>
    <a href="<?= e(service_url('branding-creative')) ?>" class="services-bar__tab">
      <?= icon('palette', 'icon') ?> <span>Branding</span>
    </a>
    <a href="<?= e(service_url('it-services-cloud')) ?>" class="services-bar__tab">
      <?= icon('cloud', 'icon') ?> <span>IT &amp; Cloud</span>
    </a>
    <a href="<?= e(service_url('ai-automation')) ?>" class="services-bar__tab">
      <?= icon('cpu', 'icon') ?> <span>AI &amp; Automation</span>
    </a>
    <a href="<?= e(service_url('growth-strategy')) ?>" class="services-bar__tab">
      <?= icon('growth', 'icon') ?> <span>Growth</span>
    </a>
  </div>
</div>

<!-- ==================== 3. OUTCOME & QUICK ANSWER SHOWCASE ==================== -->
<section class="section" id="quick-answer" aria-labelledby="quickAnswerTitle">
  <div class="container">
    <div class="showcase-grid">
      <!-- Left Card: Quick Answer -->
      <article class="showcase-card" data-reveal>
        <div class="code-orb-badge" aria-hidden="true">
          &lt;/&gt;
        </div>
        <p class="eyebrow">QUICK ANSWER</p>
        <h2 class="showcase-card__title" id="quickAnswerTitle">
          Custom software development means building digital solutions that fit your business — your processes, your goals, your way.
        </h2>
        <p class="showcase-card__text">
          It includes web, mobile and desktop applications, integrated systems (ERP/CRM), and automation tools that help you work smarter, serve customers better and grow faster.
        </p>
      </article>

      <!-- Right Card: The Outcome -->
      <article class="showcase-card" data-reveal>
        <p class="eyebrow">THE OUTCOME</p>
        <h2 class="showcase-card__title">
          Design software around the way your business works.
        </h2>
        <p class="showcase-card__text">
          Get powerful systems that simplify operations, bring your data together and give you the control you need — all in one place.
        </p>

        <div class="outcome-features-grid">
          <div class="outcome-feature-item"><?= icon('users', 'icon') ?> CRM</div>
          <div class="outcome-feature-item"><?= icon('layers', 'icon') ?> ERP</div>
          <div class="outcome-feature-item"><?= icon('briefcase', 'icon') ?> Inventory</div>
          <div class="outcome-feature-item"><?= icon('shield', 'icon') ?> Billing</div>
          <div class="outcome-feature-item"><?= icon('cpu', 'icon') ?> Operations</div>
          <div class="outcome-feature-item"><?= icon('chart', 'icon') ?> Analytics</div>
        </div>

        <div class="outcome-preview-wrap">
          <svg viewBox="0 0 400 130" width="100%" height="130" style="background:#ffffff;display:block">
            <rect x="15" y="15" width="115" height="100" rx="8" fill="#f8fafc" stroke="#e2e8f0"/>
            <rect x="25" y="25" width="50" height="8" rx="4" fill="#cbd5e1"/>
            <rect x="25" y="42" width="75" height="14" rx="4" fill="#ef4444" fill-opacity="0.15"/>
            <rect x="25" y="65" width="95" height="6" rx="3" fill="#e2e8f0"/>
            <rect x="25" y="78" width="80" height="6" rx="3" fill="#e2e8f0"/>

            <rect x="145" y="15" width="240" height="100" rx="8" fill="#f8fafc" stroke="#e2e8f0"/>
            <rect x="160" y="25" width="90" height="8" rx="4" fill="#0f172a"/>
            <path d="M 160 90 Q 210 50 260 70 T 360 40" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round"/>
            <circle cx="260" cy="70" r="3.5" fill="#ef4444"/>
            <circle cx="360" cy="40" r="3.5" fill="#ef4444"/>
          </svg>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ==================== 4. BEFORE VS AFTER ("FROM CHAOS TO CONTROL") ==================== -->
<section class="section section--soft" id="comparison" aria-labelledby="compareTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">BEFORE VS AFTER</p>
      <h2 class="section-head__title" id="compareTitle">From chaos to control.</h2>
      <p class="lead">
        Without the right system, you lose time, data and opportunities. With our solution, everything works together — smoothly and efficiently.
      </p>
    </header>

    <div class="compare-wrapper" data-reveal>
      <div class="compare-container">
        <!-- Without Box -->
        <div class="compare-box compare-box--without">
          <div>
            <div class="compare-box__head">WITHOUT THE RIGHT SYSTEM</div>
            <ul class="compare-list">
              <li class="compare-list__item compare-list__item--bad">
                <span class="icon">✕</span> <span>Files and spreadsheets</span>
              </li>
              <li class="compare-list__item compare-list__item--bad">
                <span class="icon">✕</span> <span>Manual work</span>
              </li>
              <li class="compare-list__item compare-list__item--bad">
                <span class="icon">✕</span> <span>Duplicate data</span>
              </li>
              <li class="compare-list__item compare-list__item--bad">
                <span class="icon">✕</span> <span>No real-time visibility</span>
              </li>
            </ul>
          </div>

          <div style="background:#fff;border:1px solid #fee2e2;border-radius:8px;padding:1rem;display:flex;align-items:center;gap:10px">
            <span style="font-size:1.4rem">📂</span>
            <span style="font-size:0.8rem;color:#991b1b;font-weight:600">Disorganized files &amp; missed follow-ups</span>
          </div>
        </div>

        <!-- Center Arrow -->
        <div class="compare-arrow-btn" aria-hidden="true">
          →
        </div>

        <!-- With Box -->
        <div class="compare-box compare-box--with">
          <div>
            <div class="compare-box__head">WITH THE RIGHT SYSTEM</div>
            <ul class="compare-list">
              <li class="compare-list__item compare-list__item--good">
                <span class="icon">✓</span> <span>Centralized data</span>
              </li>
              <li class="compare-list__item compare-list__item--good">
                <span class="icon">✓</span> <span>Automation</span>
              </li>
              <li class="compare-list__item compare-list__item--good">
                <span class="icon">✓</span> <span>Dashboards &amp; reports</span>
              </li>
              <li class="compare-list__item compare-list__item--good">
                <span class="icon">✓</span> <span>Controlled workflows</span>
              </li>
            </ul>
          </div>

          <div style="background:#fff;border:1px solid #d1fae5;border-radius:8px;padding:1rem;display:flex;align-items:center;gap:10px">
            <span style="font-size:1.4rem">💻</span>
            <span style="font-size:0.8rem;color:#065f46;font-weight:600">Centralized dashboard on all devices</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==================== 5. WHAT WE DELIVER (6 FEATURE CARDS) ==================== -->
<section class="section" id="deliverables" aria-labelledby="deliverablesTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">WHAT WE DELIVER</p>
      <h2 class="section-head__title" id="deliverablesTitle">
        Complete software solutions for every stage of your business.
      </h2>
    </header>

    <div class="interactive-card-grid" style="margin-top:1.5rem">
      <!-- 1. Custom Software / ERP -->
      <article class="card interactive-card" data-reveal>
        <header class="card__thumb">
          <a href="<?= e(service_url('software-development')) ?>">
            <img src="<?= e(asset('images/services/software-development.jpg')) ?>" alt="Software Development"/>
          </a>
        </header>
       
        <div class="card__body">
          <div class="card__category"><a href="<?= e(service_url('software-development')) ?>">SERVICE</a></div>
          <h2 class="card__title"><a href="<?= e(service_url('software-development')) ?>">Custom Software</a></h2>
          <div class="card__subtitle">ERP, CRM &amp; Operations</div>
          <p class="card__description">
            Custom systems built around your unique business operations, eliminating spreadsheets and automating workflows with real-time reporting.
                        Custom systems built around your unique business operations, eliminating spreadsheets and automating workflows with real-time reporting.

          </p>
        </div>
        <footer class="card__footer">
          <span><?= icon('code', 'icon') ?> Scalable Architecture</span>
          <a href="<?= e(service_url('software-development')) ?>">Explore <?= icon('arrow', 'icon') ?></a>
        </footer>
      </article>

      <!-- 2. Websites & Stores -->
      <article class="card interactive-card" data-reveal>
        <header class="card__thumb">
          <a href="<?= e(service_url('website-development')) ?>">
            <img src="<?= e(asset('images/services/website-development.jpg')) ?>" alt="Website Development"/>
          </a>
        </header>
        
        <div class="card__body">
          <div class="card__category"><a href="<?= e(service_url('website-development')) ?>">SERVICE</a></div>
          <h2 class="card__title"><a href="<?= e(service_url('website-development')) ?>">Web &amp; E-Commerce</a></h2>
          <div class="card__subtitle">Fast, SEO-Ready Web Apps</div>
          <p class="card__description">
            High-converting websites and storefronts engineered for blazing speed, mobile responsiveness, and measurable business enquiries.
                        High-converting websites and storefronts engineered for blazing speed, mobile responsiveness, and measurable business enquiries.

          </p>
        </div>
        <footer class="card__footer">
          <span><?= icon('globe', 'icon') ?> Core Web Vitals</span>
          <a href="<?= e(service_url('website-development')) ?>">Explore <?= icon('arrow', 'icon') ?></a>
        </footer>
      </article>

      <!-- 3. AI & Automation -->
      <article class="card interactive-card" data-reveal>
        <header class="card__thumb">
          <a href="<?= e(service_url('ai-automation')) ?>">
            <img src="<?= e(asset('images/services/ai-automation.jpg')) ?>" alt="AI & Automation"/>
          </a>
        </header>
        
        <div class="card__body">
          <div class="card__category"><a href="<?= e(service_url('ai-automation')) ?>">SERVICE</a></div>
          <h2 class="card__title"><a href="<?= e(service_url('ai-automation')) ?>">AI &amp; Automation</a></h2>
          <div class="card__subtitle">Intelligent Workflows &amp; RPA</div>
          <p class="card__description">
            Integrate neural models, document extraction, and robotic process automation to save team hours and scale operations seamlessly.
                        Integrate neural models, document extraction, and robotic process automation to save team hours and scale operations seamlessly.

          </p>
        </div>
        <footer class="card__footer">
          <span><?= icon('cpu', 'icon') ?> Smart Pipelines</span>
          <a href="<?= e(service_url('ai-automation')) ?>">Explore <?= icon('arrow', 'icon') ?></a>
        </footer>
      </article>

      <!-- 4. Medvora Product -->
      <article class="card interactive-card" data-reveal>
        <header class="card__thumb">
          <a href="<?= e(product_url('medvora')) ?>">
            <img src="<?= e(asset('images/products/medvora.jpg')) ?>" alt="Medvora Hospital Management"/>
          </a>
        </header>
      
        <div class="card__body">
          <div class="card__category"><a href="<?= e(product_url('medvora')) ?>">PLATFORM</a></div>
          <h2 class="card__title"><a href="<?= e(product_url('medvora')) ?>">Medvora ERP</a></h2>
          <div class="card__subtitle">Hospital &amp; Clinic Suite</div>
          <p class="card__description">
            Front desk to pharmacy on a single patient record — appointments, IPD/OPD, lab diagnostics, and insurance billing in one platform. 
                                    Front desk to pharmacy on a single patient record — appointments, IPD/OPD, lab diagnostics, and insurance billing in one platform. 

          </p>
        </div>
        <footer class="card__footer">
          <span><?= icon('shield', 'icon') ?> Healthcare Suite</span>
          <a href="<?= e(product_url('medvora')) ?>">Live Demo <?= icon('arrow', 'icon') ?></a>
        </footer>
      </article>

      <!-- 5. RouteFlow Product -->
      <article class="card interactive-card" data-reveal>
        <header class="card__thumb">
          <a href="<?= e(product_url('routeflow')) ?>">
            <img src="<?= e(asset('images/products/routeflow.jpg')) ?>" alt="RouteFlow Logistics"/>
          </a>
        </header>
       
        <div class="card__body">
          <div class="card__category"><a href="<?= e(product_url('routeflow')) ?>">PLATFORM</a></div>
          <h2 class="card__title"><a href="<?= e(product_url('routeflow')) ?>">RouteFlow</a></h2>
          <div class="card__subtitle">Logistics &amp; Dispatch</div>
          <p class="card__description">
            Plan delivery routes, track fleets in real-time, capture digital proof of delivery at doorstep, and automate trip invoicing.
                        Plan delivery routes, track fleets in real-time, capture digital proof of delivery at doorstep, and automate trip invoicing.

          </p>
        </div>
        <footer class="card__footer">
          <span><?= icon('layers', 'icon') ?> Fleet &amp; GPS</span>
          <a href="<?= e(product_url('routeflow')) ?>">Live Demo <?= icon('arrow', 'icon') ?></a>
        </footer>
      </article>

      <!-- 6. Bizora Product -->
      <article class="card interactive-card" data-reveal>
        <header class="card__thumb">
          <a href="<?= e(product_url('bizora')) ?>">
            <img src="<?= e(asset('images/products/bizora.jpg')) ?>" alt="Bizora Sales CRM"/>
          </a>
        </header>
        
        <div class="card__body">
          <div class="card__category"><a href="<?= e(product_url('bizora')) ?>">PLATFORM</a></div>
          <h2 class="card__title"><a href="<?= e(product_url('bizora')) ?>">Bizora CRM</a></h2>
          <div class="card__subtitle">Lead &amp; Pipeline Engine</div>
          <p class="card__description">
            Capture enquiries from web, WhatsApp and calls, manage visual deal stages with reminders so no revenue opportunity is lost.
          </p>
        </div>
        <footer class="card__footer">
          <span><?= icon('target', 'icon') ?> Sales Pipeline</span>
          <a href="<?= e(product_url('bizora')) ?>">Live Demo <?= icon('arrow', 'icon') ?></a>
        </footer>
      </article>
    </div>
  </div>
</section>

<!-- ==================== 6. OUR PROCESS (4-STEP ROADMAP TIMELINE) ==================== -->
<section class="section section--soft" id="process" aria-labelledby="processTitle">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="eyebrow">OUR PROCESS</p>
      <h2 class="section-head__title" id="processTitle">
        A clear path from idea to <span class="accent-cyan">impact.</span>
      </h2>
      <p class="lead">
        We follow a simple, focused process to make sure your software is built right, delivered on time, and ready to scale.
      </p>
    </header>

    <div class="process-timeline" data-reveal>
      <!-- Step 01 -->
      <div class="process-step">
        <div class="process-step__node">01</div>
        <h3 class="process-step__title">Understand Your Business</h3>
        <p class="process-step__text">We learn your goals, challenges and users.</p>
      </div>

      <!-- Step 02 -->
      <div class="process-step">
        <div class="process-step__node">02</div>
        <h3 class="process-step__title">Plan The System</h3>
        <p class="process-step__text">We design the right solution and roadmap.</p>
      </div>

      <!-- Step 03 -->
      <div class="process-step">
        <div class="process-step__node">03</div>
        <h3 class="process-step__title">Build In Clear Stages</h3>
        <p class="process-step__text">We develop, test and keep you in the loop.</p>
      </div>

      <!-- Step 04 -->
      <div class="process-step">
        <div class="process-step__node">04</div>
        <h3 class="process-step__title">Launch, Train &amp; Support</h3>
        <p class="process-step__text">We go live, train your team and stay with you.</p>
      </div>
    </div>
  </div>
</section>

<?php 

require __DIR__ . '/includes/footer.php'; ?>
