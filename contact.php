<?php
/**
 * Prospect Digital — contact page (Recreated Modern Glassmorphic SaaS)
 * ---------------------------------------------------------------------------
 * Handles the enquiry form server-side:
 *   1. process_enquiry() validates CSRF, spam traps, rate limits and fields.
 *   2. On success the enquiry is stored in MySQL and JSONL backup logs.
 *   3. Supports AJAX / fetch submission with JSON responses and native fallback.
 * ---------------------------------------------------------------------------
 */
require_once __DIR__ . '/includes/config.php';

$form_errors = [];
$form_values = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $result              = process_enquiry();
    $form_errors         = $result['errors'];
    $form_field_errors   = $result['field_errors'];
    $form_general_errors = $result['general_errors'];
    $form_values         = $result['values'];

    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

    if ($is_ajax) {
        header('Content-Type: application/json; charset=utf-8');
        if ($result['ok']) {
            echo json_encode([
                'ok'        => true,
                'reference' => $result['reference'] ?? '',
                'message'   => 'Thank you — your enquiry has been received.',
                'redirect'  => url('contact') . '?sent=1#enquiry',
            ]);
        } else {
            http_response_code(422);
            echo json_encode([
                'ok'             => false,
                'errors'         => $form_errors,
                'field_errors'   => $form_field_errors,
                'general_errors' => $form_general_errors,
            ]);
        }
        exit;
    }

    if ($result['ok']) {
        // Post-redirect-get: the success message travels in the session flash.
        header('Location: ' . url('contact') . '?sent=1#enquiry', true, 303);
        exit;
    }
}

$flash = take_flash();

$page_title       = 'Contact Prospect Digital — Bhopal Office, Phone, WhatsApp & Enquiry Form';
$page_description = 'Talk to Prospect Digital in Bhopal about software, websites, marketing, cloud or automation. Call +91 7000-12-7225, WhatsApp us or send an enquiry — we reply within one working day.';
$body_class       = 'page-contact';
$hero_slug        = 'contact-office';

$breadcrumbs = [
    ['name' => 'Contact', 'url' => 'contact'],
];

$page_jsonld = [[
    '@context'    => 'https://schema.org',
    '@type'       => 'ContactPage',
    'name'        => 'Contact Prospect Digital',
    'url'         => absolute_url('contact'),
    'description' => 'Contact details, office address and enquiry form for Prospect Digital, Bhopal.',
]];

// Pre-select a service when arriving from a service page: /contact.php?service=Software+Development
$prefill_service = isset($_GET['service']) ? clean_text((string) $_GET['service'], 120) : '';
$valid_services  = array_keys(service_options());
if ($prefill_service !== '' && !in_array($prefill_service, $valid_services, true)) {
    $prefill_service = '';
}

$cur_user = current_user();
if ($cur_user && empty($form_values)) {
    $form_values['name']  = $cur_user['name'];
    $form_values['email'] = $cur_user['email'];
    $form_values['phone'] = $cur_user['phone'] ?? '';
}

require __DIR__ . '/includes/header.php';
?>

<!-- Ambient Animated Background Canvas Wrapper -->
<div class="contact-ambient-wrap">
  <div class="contact-tech-grid" aria-hidden="true"></div>
  <div class="contact-orb contact-orb--1" aria-hidden="true"></div>
  <div class="contact-orb contact-orb--2" aria-hidden="true"></div>
  <div class="contact-orb contact-orb--3" aria-hidden="true"></div>

  <!-- ==================== 1. HERO SECTION ==================== -->
  <section class="contact-hero-modern">
    <div class="container">
      <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>

      <div style="margin-top:1.5rem" data-reveal>
        <div class="contact-badge-live">
          <span class="pulse-dot"></span>
          <span>Bhopal Headquarters • Active &amp; Ready to Build</span>
        </div>

        <h1 class="contact-hero__title">
          Let’s build something <span class="contact-title-accent">extraordinary together.</span>
        </h1>

        <p class="contact-hero__lead">
          Have an idea, new project, or existing software that needs scaling? Talk directly with our senior software engineers and product architects in Bhopal.
        </p>

        <div class="contact-stat-chips">
          <span class="contact-stat-chip">
            <?= icon('check', 'icon') ?> Free Architecture &amp; Scoping
          </span>
          <span class="contact-stat-chip">
            <?= icon('shield', 'icon') ?> Strict NDAs Signed
          </span>
          <span class="contact-stat-chip">
            <?= icon('clock', 'icon') ?> Same-Day Engineer Response
          </span>
          <span class="contact-stat-chip">
            <?= icon('target', 'icon') ?> Direct Bhopal Engineering Hub
          </span>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== 2. MAIN BENTO GRID & FORM ==================== -->
  <section class="section" id="enquiry" aria-labelledby="enquiryTitle" style="padding-top:1rem;position:relative;z-index:3">
    <div class="container">
      <div class="contact-layout">

        <!-- Left Column: The Interactive Glassmorphic Enquiry Form -->
        <div class="contact-glass-card" id="enquiryFormContainer">
          <?php if ($flash && ($flash['type'] ?? '') === 'success'): ?>
            <?php
            $ref = $flash['reference'] ?? 'PD-REC-' . strtoupper(substr(md5(uniqid()), 0, 5));
            ?>
            <div class="enquiry-success-glass" id="enquiryResult" role="status" tabindex="-1">
              <div class="enquiry-success__icon">
                <?= icon('check', 'icon') ?>
              </div>
              <h2 class="enquiry-success__title">Enquiry Received!</h2>
              <p class="enquiry-success__lead">
                Thank you for reaching out. Our engineering team has received your project details and will review your requirements within one working day.
              </p>

              <div class="enquiry-ref-box">
                <span style="font-size:0.85rem;color:var(--ink-soft)">Tracking Ref:</span>
                <span class="enquiry-ref-code" id="enquiryRefText"><?= e($ref) ?></span>
                <button type="button" class="btn-copy-ref" id="copyRefBtn" title="Copy Reference">
                  Copy
                </button>
              </div>

              <div style="display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:0.85rem">
                <a class="btn btn--brand btn--sm" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
                  <?= icon('whatsapp', 'icon btn__icon') ?> Continue on WhatsApp
                </a>
                <a class="btn btn--outline btn--sm" href="<?= e(COMPANY_PHONE_URL) ?>">
                  <?= icon('phone', 'icon btn__icon') ?> Call <?= e(COMPANY_PHONE_DISPLAY) ?>
                </a>
              </div>
            </div>
          <?php else: ?>
            <div class="form-header-row">
              <div>
                <h2 class="form-header-title" id="enquiryTitle">Send an Enquiry</h2>
                <p class="form-header-sub">Tell us about your project requirements or challenges.</p>
              </div>
              <div class="form-security-badge">
                <?= icon('shield', 'icon') ?>
                <span>Direct to Engineers</span>
              </div>
            </div>

            <?php
            $form_variant = 'full';
            $form_context = 'contact-page';
            require __DIR__ . '/includes/form.php';
            ?>
          <?php endif; ?>
        </div>

        <!-- Right Column: Interactive Contact Channel Bento Cards -->
        <aside class="contact-channels-stack" aria-label="Direct contact channels">

          <!-- WhatsApp Bento Card -->
          <a class="channel-card channel-card--whatsapp" href="<?= e(COMPANY_WHATSAPP_URL) ?>" target="_blank" rel="noopener">
            <div class="channel-card__top">
              <div class="channel-card__icon-wrap">
                <?= icon('whatsapp', 'icon') ?>
              </div>
              <span class="channel-card__badge badge-whatsapp-online">
                <span class="pulse-dot"></span> Online Now
              </span>
            </div>
            <h3 class="channel-card__title">
              <span>Chat on WhatsApp</span>
              <span class="channel-card__arrow"><?= icon('arrow', 'icon') ?></span>
            </h3>
            <p class="channel-card__desc">
              Fastest reply during business hours. Great for quick questions, quotes, and scoping discussions.
            </p>
            <span class="channel-card__action">
              Open WhatsApp Chat &rarr;
            </span>
          </a>

          <!-- Direct Phone Call Card -->
          <a class="channel-card channel-card--phone" href="<?= e(COMPANY_PHONE_URL) ?>">
            <div class="channel-card__top">
              <div class="channel-card__icon-wrap">
                <?= icon('phone', 'icon') ?>
              </div>
              <span class="channel-card__badge badge-phone-hours">
                <?= icon('clock', 'icon') ?> Mon–Sat, 10 AM – 7 PM
              </span>
            </div>
            <h3 class="channel-card__title">
              <span><?= e(COMPANY_PHONE_DISPLAY) ?></span>
              <span class="channel-card__arrow"><?= icon('arrow', 'icon') ?></span>
            </h3>
            <p class="channel-card__desc">
              Direct line to our Bhopal office. Speak immediately with a senior technical consultant.
            </p>
            <span class="channel-card__action">
              Tap to Call Direct &rarr;
            </span>
          </a>

          <!-- Office & Map Bento Card -->
          <div class="channel-card channel-card--office">
            <div class="channel-card__top">
              <div class="channel-card__icon-wrap">
                <?= icon('pin', 'icon') ?>
              </div>
              <span class="channel-card__badge badge-phone-hours">
                Zone-1, M.P. Nagar
              </span>
            </div>
            <h3 class="channel-card__title">
              <span>Visit Bhopal Office</span>
            </h3>
            <p class="channel-card__desc" style="margin-bottom:0.4rem">
              <?= e(COMPANY_NAME) ?>, R-52 First Floor Gulab Vila, near Hotel Shree Vatika &amp; Chetak Bridge, Bhopal, MP 462011.
            </p>
            <a class="btn btn--outline btn--sm" style="margin-top:0.35rem;display:inline-flex;width:auto"
               href="https://www.google.com/maps/search/?api=1&amp;query=<?= urlencode('R-52 First Floor Gulab Vila, near Hotel Shree Vatika & Chetak Bridge, Zone-1, M.P. Nagar, Bhopal, Madhya Pradesh 462011') ?>"
               target="_blank" rel="noopener">
              <?= icon('pin', 'icon btn__icon') ?> Get Google Maps Directions
            </a>

            <!-- Embedded Interactive Map -->
            <div class="map-embed-container">
              <iframe
                title="Google Maps Bhopal Office Location"
                src="https://www.google.com/maps?q=<?= urlencode('R-52 First Floor Gulab Vila, near Hotel Shree Vatika & Chetak Bridge, Zone-1, M.P. Nagar, Bhopal, Madhya Pradesh 462011') ?>&amp;output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
            </div>
          </div>

          <!-- Direct Email Card -->
          <div class="channel-card channel-card--email">
            <div class="channel-card__top">
              <div class="channel-card__icon-wrap">
                <?= icon('mail', 'icon') ?>
              </div>
              <span class="channel-card__badge badge-phone-hours">
                1-Day Response
              </span>
            </div>
            <h3 class="channel-card__title">
              <span><?= e(COMPANY_EMAIL) ?></span>
            </h3>
            <p class="channel-card__desc">
              Send RFP documents, RFQ requests, technical architecture specs, or tender documents.
            </p>
            <div style="display:flex;align-items:center;gap:0.75rem;margin-top:0.25rem">
              <a class="channel-card__action" href="<?= e(COMPANY_EMAIL_URL) ?>">
                Compose Email &rarr;
              </a>
              <button type="button" class="btn-copy-ref" id="copyEmailBtn" data-email="<?= e(COMPANY_EMAIL) ?>" title="Copy email address">
                Copy Email
              </button>
            </div>
          </div>

        </aside>

      </div>
    </div>
  </section>

  <!-- ==================== 3. WHAT HAPPENS NEXT (4-STEP TIMELINE) ==================== -->
  <section class="process-section">
    <div class="container">
      <header class="section-head section-head--center" data-reveal>
        <p class="eyebrow">TRANSPARENT PROCESS</p>
        <h2 class="section-head__title">What happens after you submit?</h2>
        <p class="section-head__text">We respect your time. Here is our straightforward four-step engagement roadmap.</p>
      </header>

      <div class="process-grid">
        <div class="process-card" data-reveal>
          <span class="process-card__number">STEP 01</span>
          <div class="process-card__icon"><?= icon('layers', 'icon') ?></div>
          <h3 class="process-card__title">Review in 1 Hour</h3>
          <p class="process-card__desc">
            A senior technical lead reviews your requirements, tech stack needs, and project complexity.
          </p>
        </div>

        <div class="process-card" data-reveal>
          <span class="process-card__number">STEP 02</span>
          <div class="process-card__icon"><?= icon('phone', 'icon') ?></div>
          <h3 class="process-card__title">Discovery Call</h3>
          <p class="process-card__desc">
            A 20-minute discussion over phone, WhatsApp or Google Meet to clarify timeline, deliverables, and goals.
          </p>
        </div>

        <div class="process-card" data-reveal>
          <span class="process-card__number">STEP 03</span>
          <div class="process-card__icon"><?= icon('chart', 'icon') ?></div>
          <h3 class="process-card__title">Scope &amp; Proposal</h3>
          <p class="process-card__desc">
            You receive an itemized technical scope of work, milestone breakdown, and transparent pricing.
          </p>
        </div>

        <div class="process-card" data-reveal>
          <span class="process-card__number">STEP 04</span>
          <div class="process-card__icon"><?= icon('cpu', 'icon') ?></div>
          <h3 class="process-card__title">Agile Build</h3>
          <p class="process-card__desc">
            Kick off sprints with dedicated project manager, continuous staging builds, and real-time updates.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== 4. FREQUENTLY ASKED QUESTIONS ==================== -->
  <section class="section" aria-labelledby="contactFaqTitle" style="padding-top:0">
    <div class="container container--narrow">
      <header class="section-head section-head--center" data-reveal>
        <p class="eyebrow">COMMON QUESTIONS</p>
        <h2 class="section-head__title" id="contactFaqTitle">Before you send your enquiry.</h2>
      </header>

      <div class="faq" data-reveal>
        <details class="faq__item" open>
          <summary class="faq__q">How fast will I hear back from your team?</summary>
          <div class="faq__a">
            <p>During working hours (Mon–Sat, 10 AM to 7 PM IST), we review enquiries within 1 to 2 hours. WhatsApp messages usually receive an immediate response from an engineer on duty.</p>
          </div>
        </details>
        <details class="faq__item">
          <summary class="faq__q">Is the technical discovery consultation really free?</summary>
          <div class="faq__a">
            <p>Yes. Our initial 30-minute discovery call and high-level architectural guidance are completely free with no obligations whatsoever.</p>
          </div>
        </details>
        <details class="faq__item">
          <summary class="faq__q">Can you sign a Non-Disclosure Agreement (NDA) first?</summary>
          <div class="faq__a">
            <p>Absolutely. We regularly sign bilateral NDAs before reviewing proprietary algorithms, confidential product workflows, or sensitive enterprise database schemas.</p>
          </div>
        </details>
        <details class="faq__item">
          <summary class="faq__q">Can we visit your office in Bhopal?</summary>
          <div class="faq__a">
            <p>Yes! Our headquarters is in Zone-1, M.P. Nagar, Bhopal. You are always welcome to meet our engineering team in person. Just drop us an enquiry or WhatsApp message so we can have coffee ready.</p>
          </div>
        </details>
      </div>
    </div>
  </section>

</div>

<?php if (!empty($form_errors)): ?>
<script>
  window.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('enquiry');
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>

