<?php
/**
 * Prospect Digital — centred navigation bar
 * ---------------------------------------------------------------------------
 * Layout: logo (left) · navigation (centre, truly centred) · LET'S TALK (right)
 * On screens below 1024px the navigation collapses into an accessible
 * hamburger drawer that is operated with real buttons (keyboard + screen
 * reader friendly) and needs no JavaScript framework.
 * ---------------------------------------------------------------------------
 */
$nav_items    = $GLOBALS['PD_NAV'] ?? [];
$services_all = all_services();
$products_all = all_products();
$cur_user     = current_user();

// Determine if user is currently inside the Store section
$is_store_section = (isset($body_class) && str_contains($body_class, 'store'))
    || (isset($hero_slug) && $hero_slug === 'store')
    || str_contains($_SERVER['REQUEST_URI'] ?? '', '/store')
    || str_contains($_SERVER['REQUEST_URI'] ?? '', '/cart')
    || str_contains($_SERVER['REQUEST_URI'] ?? '', '/checkout')
    || str_contains($_SERVER['REQUEST_URI'] ?? '', '/order-confirmation');

$cart_count = 0;
if ($is_store_section) {
    require_once __DIR__ . '/store-functions.php';
    $cart_count = store_cart_count();
}
?>
<header class="site-header" id="siteHeader">
  <div class="container site-header__inner">

    <!-- Left: logo -->
    <a class="site-logo" href="<?= e(url('')) ?>" aria-label="<?= e(COMPANY_NAME) ?> — home">
      <?php require __DIR__ . '/logo-mark.php'; ?>
      <span class="site-logo__text">
        <span class="site-logo__name">Prospect</span>
        <span class="site-logo__sub">Digital</span>
      </span>
    </a>

    <!-- Centre: navigation -->
    <nav class="site-nav" id="primaryNav" tabindex="-1" aria-label="Primary navigation">
      <ul class="site-nav__list">
        <?php foreach ($nav_items as $item):
            $is_dropdown = $item['folder'] ?? false;
            $path        = $item['path'];
            ?>
          <li class="site-nav__item<?= $is_dropdown ? ' site-nav__item--has-menu' : '' ?><?= $path === 'store' ? ' site-nav__item--store' : '' ?>">
            <a class="site-nav__link<?= e(nav_state($path, (bool) $is_dropdown)) ?><?= $path === 'store' ? ' site-nav__link--store' : '' ?>" href="<?= e(url($path)) ?>"<?= nav_aria($path, (bool) $is_dropdown) ?>>
              <?php if ($path === 'store'): ?>
                <svg class="site-nav__store-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
              <?php endif; ?>
              <?= e($item['label']) ?>
            </a>

            <?php if ($is_dropdown && ($item['label'] === 'Service' || $item['label'] === 'Services')): ?>
              <button class="site-nav__toggle" type="button" aria-expanded="false" aria-controls="servicesMenu" aria-label="Toggle Services submenu">
                <span class="sr-only">Show services</span>
                <svg class="icon icon--chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div class="mega-menu mega-menu--services" id="servicesMenu" hidden>
                <div class="mega-menu__inner">
                  <div class="mega-menu__header">
                    <span class="mega-menu__label">Services</span>
                  </div>
                  <ul class="mega-menu__grid">
                    <?php foreach ($services_all as $nav_slug => $nav_svc): ?>
                      <li>
                        <a class="mega-menu__item" href="<?= e(service_url($nav_slug)) ?>">
                          <span class="mega-menu__icon" aria-hidden="true">
                            <?= icon($nav_svc['icon'] ?? 'code', 'icon') ?>
                          </span>
                          <span class="mega-menu__name"><?= e($nav_svc['name']) ?></span>
                          <svg class="mega-menu__arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <div class="mega-menu__footer">
                    <a class="mega-menu__footer-link" href="<?= e(url('services')) ?>">
                      <span>View all 8 services</span>
                      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                  </div>
                </div>
              </div>

            <?php elseif ($is_dropdown && ($item['label'] === 'Product' || $item['label'] === 'Products')): ?>
              <button class="site-nav__toggle" type="button" aria-expanded="false" aria-controls="productsMenu" aria-label="Toggle Products submenu">
                <span class="sr-only">Show products</span>
                <svg class="icon icon--chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div class="mega-menu mega-menu--products" id="productsMenu" hidden>
                <div class="mega-menu__inner">
                  <div class="mega-menu__header">
                    <span class="mega-menu__label">Products</span>
                  </div>
                  <ul class="mega-menu__list">
                    <?php foreach ($products_all as $nav_slug => $nav_prod): ?>
                      <li>
                        <a class="mega-menu__item" href="<?= e(product_url($nav_slug)) ?>">
                          <span class="mega-menu__badge" aria-hidden="true"><?= e($nav_prod['monogram']) ?></span>
                          <span class="mega-menu__name"><?= e($nav_prod['name']) ?></span>
                          <svg class="mega-menu__arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <div class="mega-menu__footer">
                    <a class="mega-menu__footer-link" href="<?= e(url('products')) ?>">
                      <span>View all platforms</span>
                      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- Mobile menu auth + CTA -->
      <div class="site-nav__mobile-auth">
        <?php if ($is_store_section): ?>
          <?php if ($cur_user): ?>
            <div class="site-nav__mobile-user">
              <span class="user-nav-avatar" aria-hidden="true"><?= e(strtoupper(mb_substr($cur_user['name'], 0, 1))) ?></span>
              <div>
                <span class="site-nav__mobile-name"><?= e($cur_user['name']) ?></span>
                <span class="site-nav__mobile-email"><?= e($cur_user['email']) ?></span>
              </div>
            </div>
            <div class="site-nav__mobile-auth-links">
              <a class="btn btn--brand btn--sm" href="<?= e(url('dashboard')) ?>" style="flex: 1; justify-content: center;">Dashboard</a>
              <a class="btn btn--outline btn--sm" href="<?= e(url('logout.php')) ?>" style="flex: 1; justify-content: center;">Sign Out</a>
            </div>
          <?php else: ?>
            <div class="site-nav__mobile-auth-links">
              <a class="btn btn--outline btn--sm" href="<?= e(url('login?return=' . urlencode(canonical_url()))) ?>" style="flex: 1; justify-content: center;">Sign In</a>
              <a class="btn btn--brand btn--sm" href="<?= e(url('signup?return=' . urlencode(canonical_url()))) ?>" style="flex: 1; justify-content: center;">Sign Up</a>
            </div>
          <?php endif; ?>
          <div class="site-nav__mobile-store" style="margin-bottom: 0.75rem;">
            <a class="btn btn--outline btn--sm" href="<?= e(url('cart')) ?>" style="width: 100%; justify-content: center; gap: 0.5rem;">
              <?= icon('shopping-bag', 'icon') ?>
              <span>Shopping Cart (<?= $cart_count ?>)</span>
            </a>
          </div>
        <?php endif; ?>
        <a class="btn btn--brand btn--lg" href="<?= e(url('contact')) ?>" style="width: 100%; justify-content: center;">
          <span>LET'S TALK</span>
          <?= icon('arrow', 'icon btn__icon') ?>
        </a>
      </div>

    </nav>

    <!-- Right: user auth + primary action + mobile toggle -->
    <div class="site-header__actions">
      <?php if ($is_store_section): ?>
        <!-- Store Cart Link with Badge -->
        <a class="nav-cart-btn" href="<?= e(url('cart')) ?>" aria-label="Shopping Cart (<?= $cart_count ?> items)" title="Shopping Cart">
          <?= icon('shopping-bag', 'icon nav-cart-icon') ?>
          <span class="nav-cart-badge" id="navCartBadge"<?= $cart_count > 0 ? '' : ' style="display:none;"' ?>><?= $cart_count ?></span>
        </a>

        <?php if ($cur_user): ?>
          <div class="site-header__user-group">
            <a class="user-nav-badge" href="<?= e(url('dashboard')) ?>" title="Account Dashboard — <?= e($cur_user['name']) ?>">
              <span class="user-nav-avatar" aria-hidden="true"><?= e(strtoupper(mb_substr($cur_user['name'], 0, 1))) ?></span>
              <span class="user-nav-name"><?= e(explode(' ', trim($cur_user['name']))[0]) ?></span>
            </a>
            <a class="btn btn--outline btn--sm site-header__logout-btn" href="<?= e(url('logout.php')) ?>" title="Sign Out">
              <span>Logout</span>
            </a>
          </div>
        <?php else: ?>
          <div class="site-header__auth">
            <a class="btn btn--outline btn--sm site-header__auth-btn site-header__auth-btn--login" href="<?= e(url('login?return=' . urlencode(canonical_url()))) ?>">
              <span>Sign In</span>
            </a>
            <a class="btn btn--brand btn--sm site-header__auth-btn site-header__auth-btn--signup" href="<?= e(url('signup?return=' . urlencode(canonical_url()))) ?>">
              <span>Sign Up</span>
            </a>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <a class="btn btn--brand btn--lg site-header__cta" href="<?= e(url('contact')) ?>">
        <span>LET'S TALK</span>
        <?= icon('arrow', 'icon btn__icon') ?>
      </a>

      <button class="nav-toggle" id="navToggle" type="button"
              aria-controls="primaryNav" aria-expanded="false" aria-label="Open navigation menu"
              onclick="if(window.pdToggleNav){window.pdToggleNav();}else{var n=document.getElementById('primaryNav'),b=document.getElementById('navBackdrop'),o=this.getAttribute('aria-expanded')==='true';n.classList.toggle('is-open',!o);this.setAttribute('aria-expanded',!o);this.classList.toggle('is-active',!o);document.body.classList.toggle('is-locked',!o);if(b){b.hidden=o;if(!o)b.classList.add('is-visible');else b.classList.remove('is-visible');}}">
        <span class="nav-toggle__bars" aria-hidden="true"><span></span><span></span><span></span></span>
      </button>
    </div>
  </div>

  <!-- Progress bar reflects scroll position; purely cosmetic and hidden from AT -->
  <div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>
</header>
<div class="nav-backdrop" id="navBackdrop" hidden>
  
</div>
