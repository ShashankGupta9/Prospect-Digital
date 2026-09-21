<?php
/**
 * Prospect Digital — Brand Logo Mark (Interlocking Geometric "PD" Icon)
 * Matches the reference template logo with deep navy 'P' and vibrant coral-red 'D'
 */
$logo_seq = ($GLOBALS['_pd_logo_seq'] = (int) ($GLOBALS['_pd_logo_seq'] ?? 0) + 1);
$logo_grad_id = 'pdBrandGrad' . $logo_seq;
$logo_class   = $logo_class ?? 'site-logo__mark';
?>
<svg class="<?= e($logo_class) ?>" viewBox="0 0 54 44" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="<?= e($logo_grad_id) ?>" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#f43f5e"/>
      <stop offset="100%" stop-color="#ef4444"/>
    </linearGradient>
  </defs>

  <!-- Left 'P' Shape (Deep Navy / Charcoal) -->
  <path d="M 6 38 L 6 12 C 6 8 9 5 13 5 L 24 5 C 29 5 33 9 33 14 C 33 19 29 23 24 23 L 14 23 L 14 38 Z M 14 11 L 14 17 L 23 17 C 24.5 17 26 15.8 26 14 C 26 12.2 24.5 11 23 11 Z"
        fill="#0b132b" class="logo-p-path"/>

  <!-- Right 'D' Shape (Vibrant Coral Red) -->
  <path d="M 22 39 C 18 39 15 36 15 32 L 15 27 L 23 27 L 23 33 L 34 33 C 39 33 43 29 43 24 C 43 19 39 15 34 15 L 29 15 L 29 9 L 34 9 C 42 9 49 16 49 24 C 49 32 42 39 34 39 Z"
        fill="url(#<?= e($logo_grad_id) ?>)" class="logo-d-path"/>
</svg>
