<?php
/**
 * Prospect Digital — Pre-footer Call to Action (Dark Curved Banner)
 * Matches the reference SaaS template banner
 */
$cta_title       = $cta_title       ?? "Let's build your system.";
$cta_description = $cta_description ?? "Get a free consultation and let's discuss how we can build the right software solution for your business.";
$cta_phone       = $cta_phone       ?? COMPANY_PHONE_DISPLAY;
?>
<section class="cta-banner-section" aria-labelledby="ctaBannerTitle">
  <div class="container">
    <div class="cta-banner" data-reveal>
      <div class="cta-banner__grid">
        <div class="cta-banner__left">
          <div class="cta-banner__icon" aria-hidden="true">
            <?php $logo_class = 'cta-banner__mark'; require __DIR__ . '/logo-mark.php'; ?>
          </div>
          <div>
            <h2 class="cta-banner__title" id="ctaBannerTitle"><?= e($cta_title) ?></h2>
            <p class="cta-banner__text"><?= e($cta_description) ?></p>
          </div>
        </div>

        <div class="cta-banner__right">
          <a class="btn btn--brand btn--lg" href="<?= e(url('contact')) ?>">
            Get a Free Consultation <?= icon('arrow', 'icon btn__icon') ?>
          </a>
          <a class="btn btn--phone-pill btn--lg" href="<?= e(COMPANY_PHONE_URL) ?>">
            <?= icon('phone', 'icon') ?> <?= e($cta_phone) ?>
          </a>
          <button class="scroll-top-btn" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Scroll to top" type="button">
            ↑
          </button>
        </div>
      </div>
    </div>
  </div>
</section>
