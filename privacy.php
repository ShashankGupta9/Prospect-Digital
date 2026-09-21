<?php
/**
 * Prospect Digital — privacy policy
 * ---------------------------------------------------------------------------
 * Written to describe what this website actually does. If you add analytics,
 * a chat widget, a CRM or a newsletter provider, update the relevant section
 * here so the policy stays true. Have your legal adviser review before launch.
 * ---------------------------------------------------------------------------
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'Privacy Policy — How Prospect Digital Handles Your Data';
$page_description = 'How Prospect Digital collects, uses, stores and protects personal information submitted through this website, and the rights you have over that data.';
$body_class       = 'page-legal page-privacy';
$page_og_type     = 'article';

$breadcrumbs = [
    ['name' => 'Privacy Policy', 'url' => 'privacy'],
];

$page_jsonld = [[
    '@context' => 'https://schema.org',
    '@type'    => 'PrivacyPolicy',
    'name'     => 'Privacy Policy — ' . COMPANY_NAME,
    'url'      => absolute_url('privacy'),
    'dateModified' => date('Y-m-d', strtotime($PD_LEGAL['last_updated'])),
]];

require __DIR__ . '/includes/header.php';
?>

<section class="service-hero" style="padding-bottom:clamp(1.5rem,4vw,2.5rem)">
  <div class="container container--narrow">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>
    <h1 style="margin-top:1.25rem">Privacy Policy</h1>
    <p class="service-hero__lead">What we collect through this website, why we collect it, how long we keep it, and how you can have it removed.</p>
    <div class="legal-meta">
      <span><?= icon('clock', 'icon') ?> Last updated: <?= e($PD_LEGAL['last_updated']) ?></span>
      <span><?= icon('shield', 'icon') ?> Applies to: <?= e(COMPANY_NAME) ?>, <?= e(COMPANY_CITY) ?>, India</span>
    </div>
  </div>
</section>

<section class="section" style="padding-top:clamp(1.5rem,4vw,2.5rem)">
  <div class="container container--narrow">
    <div class="row" style="display:grid;gap:2.5rem">

      <nav class="toc" aria-labelledby="tocTitle">
        <p class="toc__title" id="tocTitle">On this page</p>
        <ol>
          <li><a href="#who-we-are">Who we are</a></li>
          <li><a href="#what-we-collect">What we collect</a></li>
          <li><a href="#why">Why we use it</a></li>
          <li><a href="#cookies">Cookies and local storage</a></li>
          <li><a href="#sharing">Sharing and third parties</a></li>
          <li><a href="#retention">How long we keep data</a></li>
          <li><a href="#security">How we protect data</a></li>
          <li><a href="#your-rights">Your rights and choices</a></li>
          <li><a href="#children">Children</a></li>
          <li><a href="#changes">Changes to this policy</a></li>
          <li><a href="#contact-us">Contact us</a></li>
        </ol>
      </nav>

      <div class="prose">
        <h2 id="who-we-are">1. Who we are</h2>
        <p>
          This website is operated by <?= e(COMPANY_NAME) ?>, a digital solutions company with its office at
          <?= e(COMPANY_ADDRESS) ?>. In this policy, "we", "us" and "our" refer to <?= e(COMPANY_NAME) ?>,
          and "you" refers to anyone visiting this website or contacting us through it.
        </p>
        <p>
          Questions about this policy can be sent to <a href="<?= e(COMPANY_EMAIL_URL) ?>"><?= e(COMPANY_EMAIL) ?></a>
          or raised on <a href="<?= e(COMPANY_PHONE_URL) ?>"><?= e(COMPANY_PHONE_DISPLAY) ?></a> during
          <?= e(COMPANY_HOURS) ?>.
        </p>

        <h2 id="what-we-collect">2. What we collect</h2>
        <h3>Information you give us</h3>
        <p>When you submit the enquiry form, or contact us by phone, WhatsApp or e-mail, we receive:</p>
        <ul>
          <li>your name;</li>
          <li>your e-mail address;</li>
          <li>your phone or WhatsApp number;</li>
          <li>your company name, if you provide it;</li>
          <li>the service you are asking about and an optional budget range;</li>
          <li>the message you write to us;</li>
          <li>the page you submitted the form from, and the time of submission.</li>
        </ul>

        <h3>Technical information recorded with an enquiry</h3>
        <p>
          To protect the form against spam and abuse, each submission is stored with the date and time,
          a shortened technical signature derived from your IP address (a one-way hash, not the IP address itself),
          and a short description of the browser used. This is used only for security and troubleshooting.
        </p>

        <div class="callout">
          <strong>We do not run advertising trackers or third-party analytics on this website.</strong>
          If we add analytics in future, this policy will be updated before that change goes live,
          and any cookie banner required by law will be introduced at the same time.
        </div>

        <h2 id="why">3. Why we use it</h2>
        <ul>
          <li><strong>To respond to your enquiry</strong> — contacting you about the requirement you described.</li>
          <li><strong>To prepare a proposal</strong> — scope, timeline and pricing for the work discussed.</li>
          <li><strong>To keep business records</strong> — quotations, contracts and invoices where an engagement proceeds.</li>
          <li><strong>To protect the service</strong> — detecting spam, abuse and automated submissions.</li>
        </ul>
        <p>
          We rely on your consent when you submit the enquiry form, and on our legitimate business interest in
          responding to enquiries, maintaining records and securing the website. Where an engagement begins,
          we also process information as necessary to perform our contract with you.
        </p>
        <p>
          We do not sell your information, and we do not use it for unrelated marketing. If we ever want to add you
          to a mailing list, we will ask separately and you will be able to opt out at any time.
        </p>

        <h2 id="cookies">4. Cookies and local storage</h2>
        <p>
          This website sets a single session cookie so that the enquiry form works correctly and can protect
          submissions against cross-site request forgery. It contains a randomly generated security token and no
          personal information, and it expires when you close your browser session.
        </p>
        <p>
          We do not set advertising or tracking cookies. Embedded services described in section 5 may set their own
          cookies once you interact with them, and their own policies apply.
        </p>

        <h2 id="sharing">5. Sharing and third parties</h2>
        <p>We share information only in these situations:</p>
        <ul>
          <li><strong>Service providers</strong> — hosting and e-mail providers who process data on our instructions under confidentiality obligations.</li>
          <li><strong>Embedded map</strong> — the contact page embeds a Google Maps frame. Google may set cookies and receive your IP address when that frame loads.</li>
          <li><strong>Messaging links</strong> — the WhatsApp links on this site open WhatsApp, whose own privacy policy then applies to that conversation.</li>
          <li><strong>Legal requirements</strong> — where we are obliged to disclose information by law or a valid authority request.</li>
        </ul>
        <p>
          Some of these providers operate infrastructure outside India. Where that is the case, we use providers that
          offer contractual protections for personal data.
        </p>

        <h2 id="retention">6. How long we keep data</h2>
        <ul>
          <li>Enquiries that do not lead to an engagement are kept for up to 24 months and then deleted.</li>
          <li>Records connected to a project or contract are kept for the period required for accounting and tax purposes.</li>
          <li>Security information attached to an enquiry is kept with the enquiry and deleted on the same schedule.</li>
        </ul>

        <h2 id="security">7. How we protect data</h2>
        <ul>
          <li>The website is served over HTTPS, and form submissions are transmitted encrypted.</li>
          <li>Submissions are validated server-side and stored in a restricted directory that is not publicly accessible.</li>
          <li>Access to enquiry records is limited to team members who need them to respond.</li>
          <li>Administrative access is protected by individual accounts and role-based permissions.</li>
        </ul>
        <p>
          No system can be guaranteed completely secure. If a breach ever affects your personal data, we will notify
          you and the relevant authorities as required by applicable law.
        </p>

        <h2 id="your-rights">8. Your rights and choices</h2>
        <p>You can ask us to:</p>
        <ul>
          <li>tell you what personal information we hold about you;</li>
          <li>correct information that is inaccurate;</li>
          <li>delete your information, where we are not required to keep it;</li>
          <li>withdraw consent for future contact;</li>
          <li>restrict or object to certain processing.</li>
        </ul>
        <p>
          Send any such request to <a href="<?= e(COMPANY_EMAIL_URL) ?>"><?= e(COMPANY_EMAIL) ?></a> with enough
          detail for us to identify the record. We respond within 30 days. If you are not satisfied with our
          response, you may approach the appropriate data protection authority in your jurisdiction.
        </p>

        <h2 id="children">9. Children</h2>
        <p>
          This website is intended for businesses and professionals. We do not knowingly collect information from
          children. If you believe a child has submitted information to us, contact us and we will delete it.
        </p>

        <h2 id="changes">10. Changes to this policy</h2>
        <p>
          We update this policy when our practices change. The "last updated" date at the top of this page always
          reflects the current version, and material changes will be highlighted on this page.
        </p>

        <h2 id="contact-us">11. Contact us</h2>
        <p>
          <strong><?= e(COMPANY_NAME) ?></strong><br>
          <?= e(COMPANY_ADDRESS_LINE_1) ?> <?= e(COMPANY_ADDRESS_LINE_2) ?><br>
          <?= e(COMPANY_ADDRESS_LINE_3) ?> <?= e(COMPANY_ADDRESS_LINE_4) ?><br>
          E-mail: <a href="<?= e(COMPANY_EMAIL_URL) ?>"><?= e(COMPANY_EMAIL) ?></a><br>
          Phone: <a href="<?= e(COMPANY_PHONE_URL) ?>"><?= e(COMPANY_PHONE_DISPLAY) ?></a><br>
          Hours: <?= e(COMPANY_HOURS) ?>
        </p>
        <p class="muted" style="font-size:var(--fs-sm)">
          This policy is provided for transparency and general information. It is not legal advice.
          Please have it reviewed by a qualified adviser for your jurisdiction before relying on it.
        </p>
      </div>
    </div>
  </div>
</section>

<?php
$cta_label       = 'PRIVACY QUESTION?';
$cta_title       = 'Ask us anything about your data.';
$cta_description = 'If something here is unclear, or you would like a record removed, write to us and we will act on it.';
require __DIR__ . '/includes/footer.php';
?>
