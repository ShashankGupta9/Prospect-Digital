<?php
/**
 * Prospect Digital — terms of service
 * ---------------------------------------------------------------------------
 * Describes how engagements with Prospect Digital normally work: quotes,
 * payments, scope, ownership, support and liability. Replace placeholders
 * (payment terms, notice periods, jurisdiction) with your final commercial
 * positions and have them reviewed by a legal adviser before launch.
 * ---------------------------------------------------------------------------
 */
require_once __DIR__ . '/includes/config.php';

$page_title       = 'Terms of Service — Working with Prospect Digital';
$page_description = 'The terms that apply to using this website and to engagements with Prospect Digital — quotations, payments, scope, intellectual property, support and liability.';
$body_class       = 'page-legal page-terms';
$page_og_type     = 'article';

$breadcrumbs = [
    ['name' => 'Terms of Service', 'url' => 'terms'],
];

$page_jsonld = [[
    '@context' => 'https://schema.org',
    '@type'    => 'WebPage',
    'name'     => 'Terms of Service — ' . COMPANY_NAME,
    'url'      => absolute_url('terms'),
    'dateModified' => date('Y-m-d', strtotime($PD_LEGAL['last_updated'])),
]];

require __DIR__ . '/includes/header.php';
?>

<section class="service-hero" style="padding-bottom:clamp(1.5rem,4vw,2.5rem)">
  <div class="container container--narrow">
    <?php require __DIR__ . '/includes/breadcrumbs.php'; ?>
    <h1 style="margin-top:1.25rem">Terms of Service</h1>
    <p class="service-hero__lead">How we work together: quotes, payments, scope, ownership of what we build, and support.</p>
    <div class="legal-meta">
      <span><?= icon('clock', 'icon') ?> Last updated: <?= e($PD_LEGAL['last_updated']) ?></span>
      <span><?= icon('pin', 'icon') ?> Governed by the laws of India</span>
    </div>
  </div>
</section>

<section class="section" style="padding-top:clamp(1.5rem,4vw,2.5rem)">
  <div class="container container--narrow">
    <nav class="toc" aria-labelledby="termsTocTitle">
      <p class="toc__title" id="termsTocTitle">On this page</p>
      <ol>
        <li><a href="#website-use">Using this website</a></li>
        <li><a href="#quotes">Quotations and proposals</a></li>
        <li><a href="#payments">Payments and taxes</a></li>
        <li><a href="#scope">Scope, changes and timelines</a></li>
        <li><a href="#client-duties">What we need from you</a></li>
        <li><a href="#ip">Intellectual property</a></li>
        <li><a href="#third-party">Third-party services and platforms</a></li>
        <li><a href="#support">Support and maintenance</a></li>
        <li><a href="#confidentiality">Confidentiality</a></li>
        <li><a href="#liability">Limitation of liability</a></li>
        <li><a href="#termination">Suspension and termination</a></li>
        <li><a href="#law">Governing law</a></li>
        <li><a href="#contact-terms">Contact</a></li>
      </ol>
    </nav>

    <div class="prose" style="margin-top:2.5rem">
      <h2 id="website-use">1. Using this website</h2>
      <p>
        The content on this website is provided for general information about <?= e(COMPANY_NAME) ?> and our services.
        You may read, print and share it for your own business purposes. You may not copy it into your own website,
        marketing material or proposal, or present it as your own work, without our written permission.
      </p>
      <p>
        We work to keep the information accurate and current, but service descriptions, timelines and examples are
        indicative. Nothing on this website is an offer, a quotation or a contractual commitment — a commitment
        exists only in a written proposal or agreement signed by both parties.
      </p>

      <h2 id="quotes">2. Quotations and proposals</h2>
      <ul>
        <li>Every quotation is based on the requirement described to us at the time of enquiry.</li>
        <li>Quotations state the scope, deliverables, assumptions, timeline and price, and remain valid for 30 days unless stated otherwise.</li>
        <li>Where the requirement is not yet clear, we propose a paid discovery or audit before a fixed quotation is issued.</li>
        <li>Government taxes are charged in addition to quoted amounts, at the rates applicable on the invoice date.</li>
      </ul>

      <h2 id="payments">3. Payments and taxes</h2>
      <ul>
        <li>Project work is milestone-based: typically an advance to schedule the work, with the balance released against agreed milestones.</li>
        <li>Monthly services such as marketing, SEO, ads management, hosting and support are billed in advance each month and renew until cancelled with notice as stated in the proposal.</li>
        <li>Third-party costs — advertising media spend, domains, hosting, licences, stock imagery — are paid by you or reimbursed at cost, and are not included in our fees unless the proposal says so.</li>
        <li>Invoices are payable within the period stated on the invoice. Delayed payment may pause work in progress and support.</li>
      </ul>

      <h2 id="scope">4. Scope, changes and timelines</h2>
      <ul>
        <li>The agreed scope is delivered as described in the proposal. New requirements are handled as a written change request with its own cost and timeline impact.</li>
        <li>Timelines are estimates made in good faith. They assume the content, approvals and access we request are provided on time.</li>
        <li>Work is delivered in stages with review points. Feedback given at the agreed review points is included; redesign after approval is treated as a change request.</li>
        <li>Delays caused by pending information or approvals from your side may shift the delivery date accordingly.</li>
      </ul>

      <h2 id="client-duties">5. What we need from you</h2>
      <ul>
        <li>One decision-maker authorised to approve work and changes.</li>
        <li>Accurate business information, content and any brand assets you want used.</li>
        <li>Access to domains, hosting, ad accounts and third-party systems where the work requires it.</li>
        <li>Timely feedback at the agreed review points.</li>
        <li>A guarantee that any material you supply is yours to use, and does not infringe someone else's rights.</li>
      </ul>

      <h2 id="ip">6. Intellectual property</h2>
      <ul>
        <li><strong>Custom work</strong> — on full payment, ownership of the deliverables created specifically for you (for example a custom website, application or brand identity) passes to you, along with the source code where the proposal states it is included.</li>
        <li><strong>Our platforms</strong> — RouteFlow, Workora, Bizora, Medvora and Schova remain our property. A subscription grants you a licence to use the platform for your business, not ownership of the software.</li>
        <li><strong>Our tools and know-how</strong> — internal libraries, components, templates and methods we use to deliver work remain ours, and may be reused on other projects.</li>
        <li><strong>Third-party assets</strong> — fonts, images, plugins and licences remain subject to their own terms, and we will tell you what licence you need to keep them running.</li>
      </ul>

      <h2 id="third-party">7. Third-party services and platforms</h2>
      <p>
        We often integrate services we do not control — cloud hosts, payment gateways, advertising platforms,
        messaging providers, AI services and mail servers. Their uptime, pricing, policy changes and technical limits
        are outside our control, and we cannot be responsible for an outage or a change of terms at a third party.
        We will, however, tell you clearly which services your project depends on and what the alternatives are.
      </p>

      <h2 id="support">8. Support and maintenance</h2>
      <ul>
        <li>Support terms — scope, response times and hours — are stated in the proposal or the maintenance agreement.</li>
        <li>Support covers the delivered scope and defects. Requests outside that scope are quoted separately.</li>
        <li>Where hosting, domains, licences or ad accounts are in your name, you are responsible for keeping them paid and current.</li>
        <li>Hosting, security and backup standards we provide are documented in writing when the engagement starts.</li>
      </ul>

      <h2 id="confidentiality">9. Confidentiality</h2>
      <p>
        We treat business information, data and credentials you share as confidential and use them only to deliver
        the agreed work. We are happy to sign your non-disclosure agreement — or provide ours — before detailed
        discussions begin. Confidentiality obligations continue after an engagement ends.
      </p>

      <h2 id="liability">10. Limitation of liability</h2>
      <ul>
        <li>We provide our services with reasonable skill and care, in line with the agreed scope.</li>
        <li>Our total liability for any claim connected with an engagement is limited to the fees paid by you for the specific service giving rise to the claim.</li>
        <li>We are not liable for indirect or consequential losses, including lost profit, lost opportunity or loss of data arising from circumstances outside our reasonable control.</li>
        <li>We are not responsible for losses caused by third-party platforms, or by changes you or your team make to delivered work.</li>
        <li>Nothing here limits liability that cannot lawfully be limited.</li>
      </ul>

      <h2 id="termination">11. Suspension and termination</h2>
      <ul>
        <li>Either party may end a monthly service by giving written notice as stated in the proposal (normally 30 days).</li>
        <li>Work already delivered and costs already committed are payable on termination.</li>
        <li>We may suspend work or a hosted service where invoices remain unpaid after the stated due date, or where a project conflicts with legal, regulatory or ethical obligations.</li>
        <li>On termination we hand over completed deliverables and, where agreed, assist with a controlled migration to your own environment.</li>
      </ul>

      <h2 id="law">12. Governing law</h2>
      <p>
        These terms and any engagement with <?= e(COMPANY_NAME) ?> are governed by the laws of India.
        Disputes are subject to the exclusive jurisdiction of the courts at Bhopal, Madhya Pradesh.
        We will always prefer to resolve a disagreement directly and in writing before it reaches that stage.
      </p>

      <h2 id="contact-terms">13. Contact</h2>
      <p>
        <strong><?= e(COMPANY_NAME) ?></strong><br>
        <?= e(COMPANY_ADDRESS_LINE_1) ?> <?= e(COMPANY_ADDRESS_LINE_2) ?><br>
        <?= e(COMPANY_ADDRESS_LINE_3) ?> <?= e(COMPANY_ADDRESS_LINE_4) ?><br>
        E-mail: <a href="<?= e(COMPANY_EMAIL_URL) ?>"><?= e(COMPANY_EMAIL) ?></a> ·
        Phone: <a href="<?= e(COMPANY_PHONE_URL) ?>"><?= e(COMPANY_PHONE_DISPLAY) ?></a>
      </p>
      <p class="muted" style="font-size:var(--fs-sm)">
        These terms are provided for transparency. They are not legal advice; please have them reviewed by a
        qualified adviser and align them with your final commercial terms before launch.
      </p>
    </div>
  </div>
</section>

<?php
$cta_label       = 'QUESTIONS ABOUT TERMS?';
$cta_title       = 'Ask before you sign anything.';
$cta_description = 'Unclear terms cause disputes. If anything here needs explaining or adjusting for your project, tell us.';
require __DIR__ . '/includes/footer.php';
?>
