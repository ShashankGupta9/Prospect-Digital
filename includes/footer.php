<?php
/**
 * Prospect Digital — site footer (Modern Dark Sleek Footer)
 * Included by every page through footer.php.
 */
$footer_year = date('Y');
?>
</main><!-- /#main -->

<?php
// Optional pre-footer CTA banner — pages can set $hide_footer_cta = true to skip it.
if (empty($hide_footer_cta)) {
    require __DIR__ . '/cta.php';
}
?>

<footer class="site-footer" id="siteFooter">
  <div class="container site-footer__grid">
    <!-- Col 1: Brand & Socials -->
    <div class="site-footer__col site-footer__col--brand">
      <a class="site-logo" href="<?= e(url('')) ?>" aria-label="<?= e(COMPANY_NAME) ?> — home" style="margin-bottom: 1.5rem; display: inline-flex;">
        <?php $logo_class = 'site-logo__mark'; require __DIR__ . '/logo-mark.php'; ?>
        <span class="site-logo__text">
          <span class="site-logo__name" style="color:#ffffff;">Prospect</span>
          <span class="site-logo__sub">Digital</span>
        </span>
      </a>
      <p class="site-footer__desc">
        We build digital solutions that deliver business growth — websites, software, cloud and marketing that make a positive impact.
      </p>
      <div class="site-footer__socials-grid">
        <!-- Chat -->
        <a class="site-footer__social-circle" href="<?= e(url('contact')) ?>" aria-label="Chat">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </a>
        <!-- Phone -->
        <a class="site-footer__social-circle" href="tel:+919999999999" aria-label="Phone">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </a>
        <!-- Email -->
        <a class="site-footer__social-circle" href="mailto:hello@prospectdigital.in" aria-label="Email">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </a>
        <!-- Location -->
        <a class="site-footer__social-circle" href="<?= e(url('contact')) ?>" aria-label="Location">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </a>
        <!-- LinkedIn -->
        <a class="site-footer__social-circle" href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
        </a>
        <!-- Instagram -->
        <a class="site-footer__social-circle" href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
        </a>
      </div>
    </div>

    <!-- Col 2: Quick Links -->
    <div class="site-footer__col">
      <h3 class="site-footer__heading">QUICK LINKS</h3>
      <ul class="site-footer__list">
        <li><a class="site-footer__link" href="<?= e(url('')) ?>">Home</a></li>
        <li><a class="site-footer__link" href="<?= e(url('about')) ?>">About</a></li>
        <li><a class="site-footer__link" href="<?= e(url('services')) ?>">Services</a></li>
        <li><a class="site-footer__link" href="<?= e(url('projects')) ?>">Projects</a></li>
        <li><a class="site-footer__link" href="<?= e(url('products')) ?>">Products</a></li>
        <li><a class="site-footer__link" href="<?= e(url('guides')) ?>">Guides</a></li>
        <li><a class="site-footer__link" href="<?= e(url('contact')) ?>">Contact</a></li>
      </ul>
    </div>

    <!-- Col 3: Services -->
    <div class="site-footer__col">
      <h3 class="site-footer__heading">OUR SERVICES</h3>
      <ul class="site-footer__list">
        <?php foreach($PD_SERVICES ?? [] as $svc): ?>
          <li><a class="site-footer__link" href="<?= e(url('services/' . $svc['slug'])) ?>"><?= e($svc['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Col 4: Products -->
    <div class="site-footer__col">
      <h3 class="site-footer__heading">OUR PRODUCTS</h3>
      <ul class="site-footer__list">
        <?php foreach($PD_PRODUCTS ?? [] as $prod): ?>
          <li>
            <a class="site-footer__link" href="<?= e(url('products/' . $prod['slug'])) ?>">
              <?= e($prod['name']) ?> &mdash; <?= e($prod['category']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Col 5: Contact / Stay Updated -->
    <div class="site-footer__col site-footer__col--contact">
      <h3 class="site-footer__heading">STAY UPDATED</h3>
      <address class="site-footer__address">
        R-52, First Floor, Gulab Vila, near<br>
        Hotel Shree Vatika & Chetak Bridge,<br>
        Zone-1, M.P. Nagar, Bhopal,<br>
        Madhya Pradesh 462011
      </address>
      <div class="site-footer__contact-info">
        <a class="site-footer__link" href="mailto:hello@prospectdigital.in">hello@prospectdigital.in</a> &middot; Mon&ndash;Sat<br>
        10:00 AM &ndash; 7:00 PM IST
      </div>
      
      <form class="newsletter-form" onsubmit="event.preventDefault();">
        <input type="email" placeholder="Enter your email" class="newsletter-form__input" aria-label="Email address" required>
        <button type="submit" class="newsletter-form__submit" aria-label="Subscribe">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </form>
      <p class="site-footer__spam-note">
        Product updates and practical growth tips.<br>
        No spam.
      </p>
    </div>
  </div>

  <div class="container site-footer__bottom">
    <p class="site-footer__copy">© <?= e($footer_year) ?> <?= e(COMPANY_NAME) ?>. All rights reserved.</p>
    <ul class="site-footer__nav-bottom">
      <li><a class="site-footer__link" href="<?= e(url('privacy')) ?>">Privacy Policy</a></li>
      <li><a class="site-footer__link" href="<?= e(url('terms')) ?>">Terms of Service</a></li>
      <li><a class="site-footer__link" href="<?= e(url('sitemap.xml')) ?>">Sitemap</a></li>
      <li>
        <a class="site-footer__link" href="<?= e(url('admin')) ?>" title="Administrative Portal" style="display:inline-flex; align-items:center; gap:4px; opacity:0.75; transition:opacity 0.2s ease;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.75">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <span>Admin</span>
        </a>
      </li>
    </ul>
  </div>
</footer>
</div><!-- /.page -->

<!-- Floating quick actions -->
<a href="<?= e(url('contact')) ?>" class="float-cta" aria-label="Book a free consultation">
  <span>⚡ Free Consultation ↗</span>
</a>

<script src="<?= e(asset('js/main.js')) ?>"></script>
<script src="<?= e(asset('js/three.r134.min.js')) ?>" defer></script>
<script src="<?= e(asset('js/vanta.birds.min.js')) ?>" defer></script>
<script src="<?= e(asset('js/vanta-init.js')) ?>" defer></script>
</body>
</html>
