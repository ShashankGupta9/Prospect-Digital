<?php
/**
 * Prospect Digital — document head
 * ---------------------------------------------------------------------------
 * Every page sets these variables *before* including this file:
 *
 *   $page_title       (string)  browser + search title
 *   $page_description (string)  meta description (≈150–160 characters)
 *   $page_keywords    (string)  optional
 *   $body_class       (string)  optional extra classes for <body>
 *   $page_og_type     (string)  optional, 'website' or 'article'
 *   $page_robots      (string)  optional, default 'index, follow'
 *   $breadcrumbs      (array)   optional [['name' => 'Services', 'url' => 'services.php']]
 *   $page_jsonld      (array)   optional extra structured-data blocks
 * ---------------------------------------------------------------------------
 */

require_once __DIR__ . '/config.php';

$page_title       = $page_title       ?? (COMPANY_NAME . ' — ' . COMPANY_TAGLINE);
$page_description = $page_description ?? COMPANY_DESCRIPTION;
$page_keywords    = $page_keywords    ?? '';
$body_class       = $body_class       ?? '';
$page_og_type     = $page_og_type     ?? 'website';
$page_robots      = $page_robots      ?? 'index, follow';
$breadcrumbs      = $breadcrumbs      ?? [];
$page_jsonld      = $page_jsonld      ?? [];

$full_title = (str_contains($page_title, COMPANY_NAME)) ? $page_title : $page_title . ' | ' . COMPANY_NAME;

// Open Graph / social preview image — falls back to the logo mark.
$og_candidates = ['images/og-default.jpg', 'images/og-default.png', 'logo/prospect-digital-mark.png'];
$og_image = null;
foreach ($og_candidates as $candidate) {
    if (is_file(dirname(__DIR__) . '/assets/' . $candidate)) {
        $og_image = absolute_url('assets/' . $candidate);
        break;
    }
}
if ($og_image === null && isset($hero_slug) && is_file(dirname(__DIR__) . '/assets/hero/' . $hero_slug . '.webp')) {
    $og_image = absolute_url('assets/hero/' . $hero_slug . '.webp');
}

$organisation = [
    '@context' => 'https://schema.org',
    '@type'    => 'ProfessionalService',
    '@id'      => rtrim(SITE_URL, '/') . '/#organisation',
    'name'     => COMPANY_NAME,
    'description' => COMPANY_DESCRIPTION,
    'slogan'   => COMPANY_TAGLINE,
    'url'      => rtrim(SITE_URL, '/') . '/',
    'email'    => COMPANY_EMAIL,
    'telephone' => '+' . COMPANY_PHONE_RAW,
    'image'    => absolute_url('assets/logo/prospect-digital-mark.png'),
    'address'  => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'R-52, First Floor, Gulab Vila, near Hotel Shree Vatika & Chetak Bridge, Zone-1, M.P. Nagar',
        'addressLocality' => COMPANY_CITY,
        'addressRegion'   => COMPANY_STATE,
        'postalCode'      => COMPANY_PINCODE,
        'addressCountry'  => 'IN',
    ],
    'geo' => [
        '@type'     => 'GeoCoordinates',
        'latitude'  => COMPANY_LATITUDE,
        'longitude' => COMPANY_LONGITUDE,
    ],
    'openingHoursSpecification' => [[
        '@type'     => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        'opens'     => '10:00',
        'closes'    => '19:00',
    ]],
    'areaServed' => ['@type' => 'Country', 'name' => 'India'],
    'sameAs'     => [],
];
?>
<!DOCTYPE html>
<html lang="en-IN" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($full_title) ?></title>
<meta name="description" content="<?= e($page_description) ?>">
<?php if ($page_keywords !== ''): ?>
<meta name="keywords" content="<?= e($page_keywords) ?>">
<?php endif; ?>
<meta name="robots" content="<?= e($page_robots) ?>">
<link rel="canonical" href="<?= e(canonical_url()) ?>">
<meta name="theme-color" content="#0B1330">
<meta name="author" content="<?= e(COMPANY_NAME) ?>">
<meta name="geo.region" content="IN-MP">
<meta name="geo.placename" content="<?= e(COMPANY_CITY) ?>">

<!-- Open Graph -->
<meta property="og:type" content="<?= e($page_og_type) ?>">
<meta property="og:site_name" content="<?= e(COMPANY_NAME) ?>">
<meta property="og:locale" content="en_IN">
<meta property="og:title" content="<?= e($full_title) ?>">
<meta property="og:description" content="<?= e($page_description) ?>">
<meta property="og:url" content="<?= e(canonical_url()) ?>">
<?php if ($og_image): ?>
<meta property="og:image" content="<?= e($og_image) ?>">
<meta property="og:image:alt" content="<?= e(COMPANY_NAME . ' — ' . COMPANY_TAGLINE) ?>">
<?php endif; ?>

<!-- Twitter -->
<meta name="twitter:card" content="<?= $og_image ? 'summary_large_image' : 'summary' ?>">
<meta name="twitter:title" content="<?= e($full_title) ?>">
<meta name="twitter:description" content="<?= e($page_description) ?>">
<?php if ($og_image): ?>
<meta name="twitter:image" content="<?= e($og_image) ?>">
<?php endif; ?>

<!-- Favicons: /assets/logo/ holds the master files; favicon.png is generated from the mark. -->
<link rel="icon" href="<?= e(url('favicon.ico')) ?>" sizes="any">
<link rel="icon" type="image/png" href="<?= e(asset('logo/favicon.png')) ?>" sizes="512x512">
<link rel="apple-touch-icon" href="<?= e(asset('logo/favicon.png')) ?>">
<link rel="manifest" href="<?= e(url('site.webmanifest')) ?>">

    <!-- Styles: one blocking stylesheet, plus print styles that are only fetched for printing -->
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/print.css')) ?>" media="print">
<!-- With JavaScript disabled, scroll-reveal must never hide content -->
<noscript><style>[data-reveal]{opacity:1 !important;transform:none !important}</style></noscript>

<!-- Fonts: Outfit (Display), Plus Jakarta Sans & Inter (Body) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<meta name="format-detection" content="telephone=no">
<link rel="preconnect" href="https://wa.me">

<script type="application/ld+json"><?= json_encode($organisation, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php if (!empty($page_jsonld)): ?>
<?php foreach ($page_jsonld as $block): ?>
<script type="application/ld+json"><?= json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($breadcrumbs)): ?>
<script type="application/ld+json"><?= json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => array_map(static function (array $crumb, int $i): array {
        $item = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb['name']];
        if (!empty($crumb['url'])) {
            $item['item'] = absolute_url($crumb['url']);
        }
        return $item;
    }, $breadcrumbs, array_keys($breadcrumbs)),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
</head>
<body class="<?= e(trim($body_class)) ?>">
<div id="vanta-birds-bg" class="vanta-birds-bg" aria-hidden="true"></div>
<a class="skip-link" href="#main">Skip to main content</a>

<div class="page" id="top">
<?php
// The navbar is included here so the page never prints a stray whitespace gap.
require __DIR__ . '/navbar.php';
?>
<main id="main" tabindex="-1">
