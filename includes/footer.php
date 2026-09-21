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
  <div class="container site-footer__inner">
    <!-- Left: Logo -->
    <a class="site-logo" href="<?= e(url('')) ?>" aria-label="<?= e(COMPANY_NAME) ?> — home">
      <?php $logo_class = 'site-logo__mark'; require __DIR__ . '/logo-mark.php'; ?>
      <span class="site-logo__text">
        <span class="site-logo__name" style="color:#ffffff;">Prospect</span>
        <span class="site-logo__sub">Digital</span>
      </span>
    </a>

    <!-- Center: Main Navigation -->
    <nav class="site-footer__nav" aria-label="Footer navigation">
      <a class="site-footer__link" href="<?= e(url('services')) ?>">Services</a>
      <a class="site-footer__link" href="<?= e(url('products')) ?>">Products</a>
      <a class="site-footer__link" href="<?= e(url('projects')) ?>">Work</a>
      <a class="site-footer__link" href="<?= e(url('store')) ?>">Store</a>
      <a class="site-footer__link" href="<?= e(url('about')) ?>">About</a>
      <a class="site-footer__link" href="<?= e(url('contact')) ?>">Contact</a>
    </nav>

    <!-- Right: Social Links -->
    <div class="site-footer__socials">
      <a class="site-footer__social-link" href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
      </a>
      <a class="site-footer__social-link" href="https://github.com" target="_blank" rel="noopener" aria-label="GitHub">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
      </a>
      <a class="site-footer__social-link" href="https://youtube.com" target="_blank" rel="noopener" aria-label="YouTube">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
      </a>
      <a class="site-footer__social-link" href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
      </a>
    </div>
  </div>

  <div class="container site-footer__bottom">
    <p class="site-footer__copy">© <?= e($footer_year) ?> <?= e(COMPANY_NAME) ?>. All rights reserved.</p>
    <ul class="site-footer__nav">
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

<?php if (!empty($_SESSION['admin_user'])): ?>
  <a href="<?= e(url('admin')) ?>" class="float-admin-chip" title="Active Admin Session" aria-label="Open Admin Console" style="position:fixed; bottom:1.5rem; left:1.5rem; z-index:9990; background:linear-gradient(135deg, #e11d48, #ef4444); color:#ffffff; padding:0.45rem 0.95rem; border-radius:999px; font-size:12px; font-weight:700; text-decoration:none; box-shadow:0 8px 24px -4px rgba(225,29,72,0.45); display:inline-flex; align-items:center; gap:6px; transition:transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
    <span style="width:7px; height:7px; background:#4ade80; border-radius:50%; display:inline-block; box-shadow:0 0 8px #4ade80;"></span>
    <span>⚡ Admin Console</span>
  </a>
<?php endif; ?>

<script src="<?= e(asset('js/main.js')) ?>"></script>
<script src="<?= e(asset('js/three.r134.min.js')) ?>" defer></script>
<script src="<?= e(asset('js/vanta.birds.min.js')) ?>" defer></script>
<script src="<?= e(asset('js/vanta-init.js')) ?>" defer></script>
</body>
</html>
