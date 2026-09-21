<?php
/**
 * Prospect Digital — enquiry form component (Recreated Modern Glassmorphic SaaS UI)
 * ---------------------------------------------------------------------------
 * Variables a page may set before including this file:
 *   $form_field_errors     (array)  field name => message, from process_enquiry()
 *   $form_general_errors   (array)  session / spam / rate-limit messages
 *   $form_errors           (array)  all messages (used for the summary block)
 *   $form_values           (array)  previously submitted values (kept on error)
 *   $form_prefill_service  (string) preselect a service (e.g. from ?service=)
 *   $form_variant          (string) 'full' (default) or 'compact'
 *   $form_context          (string) label used in the tracking copy
 * ---------------------------------------------------------------------------
 */
$form_field_errors    = $form_field_errors ?? [];
$form_general_errors  = $form_general_errors ?? [];
$form_errors          = $form_errors ?? array_merge($form_general_errors, array_values($form_field_errors));
$form_values          = $form_values ?? [];
$form_variant         = $form_variant ?? 'full';
$form_context         = $form_context ?? 'enquiry';
$form_prefill_service = $form_prefill_service ?? '';
$form_uid             = 'f' . substr(md5(uniqid('', true)), 0, 6);

/** Print a submitted value safely. */
$value = static function (string $key, string $default = '') use ($form_values): string {
    return e($form_values[$key] ?? $default);
};

/** aria attributes for a field that failed validation. */
$aria = static function (string $field) use ($form_field_errors, $form_uid): string {
    if (empty($form_field_errors[$field])) {
        return '';
    }
    return ' aria-invalid="true" aria-describedby="' . e($form_uid . '-' . $field . '-error') . '"';
};

/** Inline error message shown directly under a field. */
$error_note = static function (string $field) use ($form_field_errors, $form_uid): string {
    if (empty($form_field_errors[$field])) {
        return '';
    }
    return '<p class="modern-field__error" id="' . e($form_uid . '-' . $field . '-error') . '">'
        . icon('close', 'icon') . '<span>' . e($form_field_errors[$field]) . '</span></p>';
};

$selected_service = $form_values['service'] ?? $form_prefill_service;
$selected_budget  = $form_values['budget'] ?? '';
?>
<form class="enquiry-form<?= $form_variant === 'compact' ? ' enquiry-form--compact' : '' ?>"
      id="<?= e($form_uid) ?>"
      action="<?= e(url('contact')) ?>#enquiry"
      method="post"
      novalidate
      data-form
      data-context="<?= e($form_context) ?>">

  <?= csrf_field() ?>
  <input type="hidden" name="form_started" value="<?= e((string) time()) ?>">
  <input type="hidden" name="form_context" value="<?= e($form_context) ?>">

  <!-- Spam trap: hidden from humans, tempting for bots -->
  <div class="hp-field" aria-hidden="true" style="display:none">
    <label for="<?= e($form_uid) ?>-website">Website (leave empty)</label>
    <input type="text" id="<?= e($form_uid) ?>-website" name="website_url" tabindex="-1" autocomplete="off">
  </div>

  <?php if ($form_errors): ?>
    <div class="alert alert--error" role="alert" tabindex="-1" id="<?= e($form_uid) ?>-errors" style="margin-bottom:1.5rem">
      <p class="alert__title">
        <?= count($form_errors) === 1 ? 'One thing needs fixing:' : 'Please fix these ' . count($form_errors) . ' items:' ?>
      </p>
      <ul class="alert__list">
        <?php foreach ($form_errors as $message): ?>
          <li><?= e($message) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- 1. Interactive Service Selection Chips -->
  <div class="chips-group">
    <span class="chips-label">
      1. What service do you need? <span class="req" aria-hidden="true">*</span>
    </span>
    <div class="chips-grid" role="group" aria-label="Select a service">
      <?php foreach (service_options() as $key => $label): ?>
        <?php $isActive = ($selected_service === $key); ?>
        <button type="button"
                class="chip-btn service-chip<?= $isActive ? ' is-active' : '' ?>"
                data-service-value="<?= e($key) ?>"
                aria-pressed="<?= $isActive ? 'true' : 'false' ?>">
          <?= icon('check', 'icon chip-icon') ?>
          <span><?= e($label) ?></span>
        </button>
      <?php endforeach; ?>
    </div>
    <!-- Hidden sync select for form submit and accessibility -->
    <select class="field__input field__input--select" id="<?= e($form_uid) ?>-service" name="service" required style="position:absolute;opacity:0;pointer-events:none;height:0;width:0"<?= $aria('service') ?>>
      <option value="">Select a service…</option>
      <?php foreach (service_options() as $key => $label): ?>
        <option value="<?= e($key) ?>"<?= $selected_service === $key ? ' selected' : '' ?>>
          <?= e($label) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?= $error_note('service') ?>
  </div>

  <?php if ($form_variant === 'full'): ?>
    <!-- 2. Interactive Budget Chips -->
    <div class="chips-group">
      <span class="chips-label">
        2. Expected project budget <span class="field__hint" style="font-weight:normal">(optional)</span>
      </span>
      <div class="chips-grid" role="group" aria-label="Select a budget range">
        <?php foreach (budget_options() as $range): ?>
          <?php $isActive = ($selected_budget === $range); ?>
          <button type="button"
                  class="chip-btn budget-chip<?= $isActive ? ' is-active' : '' ?>"
                  data-budget-value="<?= e($range) ?>"
                  aria-pressed="<?= $isActive ? 'true' : 'false' ?>">
            <span><?= e($range) ?></span>
          </button>
        <?php endforeach; ?>
      </div>
      <select class="field__input field__input--select" id="<?= e($form_uid) ?>-budget" name="budget" style="position:absolute;opacity:0;pointer-events:none;height:0;width:0">
        <option value="">Select a range…</option>
        <?php foreach (budget_options() as $range): ?>
          <option value="<?= e($range) ?>"<?= $selected_budget === $range ? ' selected' : '' ?>><?= e($range) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php endif; ?>

  <!-- 3. Contact Details Grid -->
  <div class="modern-field-row modern-field-row--2">
    <div class="modern-field">
      <label class="modern-field__label" for="<?= e($form_uid) ?>-name">
        Your Name <span class="req" aria-hidden="true">*</span>
      </label>
      <div class="modern-input-wrap">
        <input class="modern-input" type="text" id="<?= e($form_uid) ?>-name" name="name"
               value="<?= $value('name') ?>" required autocomplete="name"
               placeholder="e.g. Vikram Malhotra"<?= $aria('name') ?>>
        <span class="modern-input-icon"><?= icon('user', 'icon') ?></span>
      </div>
      <?= $error_note('name') ?>
    </div>

    <div class="modern-field">
      <label class="modern-field__label" for="<?= e($form_uid) ?>-phone">
        Phone / WhatsApp <span class="req" aria-hidden="true">*</span>
      </label>
      <div class="modern-input-wrap">
        <input class="modern-input" type="tel" id="<?= e($form_uid) ?>-phone" name="phone"
               value="<?= $value('phone') ?>" required autocomplete="tel" inputmode="tel"
               placeholder="+91 98260 00000"<?= $aria('phone') ?>>
        <span class="modern-input-icon"><?= icon('phone', 'icon') ?></span>
      </div>
      <?= $error_note('phone') ?>
    </div>
  </div>

  <div class="modern-field-row modern-field-row--2">
    <div class="modern-field">
      <label class="modern-field__label" for="<?= e($form_uid) ?>-email">
        Work E-mail <span class="req" aria-hidden="true">*</span>
      </label>
      <div class="modern-input-wrap">
        <input class="modern-input" type="email" id="<?= e($form_uid) ?>-email" name="email"
               value="<?= $value('email') ?>" required autocomplete="email"
               placeholder="vikram@company.com"<?= $aria('email') ?>>
        <span class="modern-input-icon"><?= icon('mail', 'icon') ?></span>
      </div>
      <?= $error_note('email') ?>
    </div>

    <div class="modern-field">
      <label class="modern-field__label" for="<?= e($form_uid) ?>-company">
        Company / Brand <span class="field__hint" style="font-weight:normal">(optional)</span>
      </label>
      <div class="modern-input-wrap">
        <input class="modern-input" type="text" id="<?= e($form_uid) ?>-company" name="company"
               value="<?= $value('company') ?>" autocomplete="organization" placeholder="Malhotra Labs Pvt Ltd">
        <span class="modern-input-icon"><?= icon('briefcase', 'icon') ?></span>
      </div>
    </div>
  </div>

  <!-- 4. Requirement Message -->
  <div class="modern-field" style="margin-bottom:1.25rem">
    <label class="modern-field__label" for="<?= e($form_uid) ?>-message">
      Briefly describe your project or challenge <span class="req" aria-hidden="true">*</span>
    </label>
    <textarea class="modern-textarea" id="<?= e($form_uid) ?>-message" name="message" rows="4" required
              placeholder="Tell us about your requirement, timeline or current tech setup. A couple of sentences is plenty."<?= $aria('message') ?>><?= $value('message') ?></textarea>
    <?= $error_note('message') ?>
  </div>

  <!-- 5. Consent Checkbox -->
  <div class="field field--consent" style="margin-bottom:1.5rem">
    <label class="checkbox" style="display:flex;align-items:flex-start;gap:0.65rem;font-size:0.84rem;color:var(--ink-soft);cursor:pointer">
      <input type="checkbox" name="consent" value="1" checked required<?= !empty($form_values['consent']) ? ' checked' : '' ?><?= $aria('consent') ?> style="margin-top:0.2rem">
      <span>I agree that <?= e(COMPANY_NAME) ?> may store and use these details to contact me regarding this enquiry, per the
        <a href="<?= e(url('privacy')) ?>" style="color:var(--brand);font-weight:600;text-decoration:underline">Privacy Policy</a>.</span>
    </label>
    <?= $error_note('consent') ?>
  </div>

  <!-- 6. Submit Action -->
  <div style="margin-bottom:1rem">
    <button class="btn--contact-submit" type="submit">
      <span class="btn-text">Send Enquiry Now</span>
      <span class="btn-arrow"><?= icon('arrow', 'icon') ?></span>
    </button>
  </div>

  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;font-size:0.8rem;color:var(--ink-muted)">
    <span style="display:inline-flex;align-items:center;gap:0.4rem">
      <?= icon('shield', 'icon') ?> 256-bit SSL encrypted • Zero spam guarantee
    </span>
    <span>Replies usually within <strong>1 hour</strong></span>
  </div>
</form>
