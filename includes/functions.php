<?php
/**
 * Prospect Digital — shared helper functions
 * ---------------------------------------------------------------------------
 * Small, readable helpers used by every page:
 *   • safe output escaping            • URL / asset builders
 *   • content lookups (services/products)
 *   • inline SVG icons & mockups      • CSRF protection
 *   • validation + contact-form handling
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

/* =========================================================================
   1. OUTPUT & URL HELPERS
   ========================================================================= */

/** Escape any value before printing it in HTML (XSS protection). */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build an internal URL: url('contact.php') → /prospect-digital/contact.php */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_URL . ($path === '' ? '/' : '/' . $path);
}

/**
 * Build an asset URL with a cache-busting version.
 *
 * The version is the file's modification time, so editing style.css or
 * main.js changes its URL automatically and every visitor receives the new
 * copy without you having to clear caches. If the file cannot be found the
 * ASSET_VERSION constant is used instead.
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    if (str_starts_with($path, 'assets/')) {
        $path = substr($path, 7);
    }
    $file = dirname(__DIR__) . '/assets/' . $path;      // project root + /assets/
    $version = is_file($file) ? (string) filemtime($file) : ASSET_VERSION;
    return BASE_URL . '/assets/' . $path . '?v=' . $version;
}

/** Absolute URL (used by canonical tags, Open Graph and the sitemap). */
function absolute_url(string $path = ''): string
{
    return rtrim(SITE_URL, '/') . url($path);
}

/** Current request path without query string, e.g. '/services/digital-marketing.php'. */
function current_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = explode('?', $uri)[0];
    $uri = explode('#', $uri)[0];
    return $uri === '' ? '/' : $uri;
}

/** Path of the current page relative to the project folder. */
function current_relative_path(): string
{
    $path = current_path();
    if (BASE_URL !== '' && str_starts_with($path, BASE_URL)) {
        $path = substr($path, strlen(BASE_URL));
    }
    return ltrim($path, '/');
}

/** Is $path (or the folder it lives in) the page being viewed right now? */
function is_current(string $path, bool $match_folder = false): bool
{
    $current = current_relative_path();
    $path    = ltrim($path, '/');

    if ($current === '' || $current === 'index.php') {
        $current = 'index.php';
    }
    if ($current === $path) {
        return true;
    }
    if ($match_folder && str_starts_with($current, $path)) {
        return true;
    }
    return false;
}

/** Returns "is-active" when the given path matches the current page. */
function nav_state(string $path, bool $match_folder = false): string
{
    return is_current($path, $match_folder) ? ' is-active' : '';
}

/** aria-current attribute for the active navigation item. */
function nav_aria(string $path, bool $match_folder = false): string
{
    return is_current($path, $match_folder) ? ' aria-current="page"' : '';
}

/** Canonical URL for the page currently being rendered. */
function canonical_url(): string
{
    $path = current_relative_path();
    if ($path === '' || $path === 'index.php') {
        return rtrim(SITE_URL, '/') . '/';
    }
    return rtrim(SITE_URL, '/') . '/' . $path;
}

/* =========================================================================
   2. CONTENT LOOKUPS
   ========================================================================= */

/** All services as an array keyed by slug. */
function all_services(): array
{
    return $GLOBALS['PD_SERVICES'] ?? [];
}

/** All products as an array keyed by slug. */
function all_products(): array
{
    return $GLOBALS['PD_PRODUCTS'] ?? [];
}

/** One service by slug, or null. */
function service(string $slug): ?array
{
    $services = all_services();
    return $services[$slug] ?? null;
}

/** One product by slug, or null. */
function product(string $slug): ?array
{
    $products = all_products();
    return $products[$slug] ?? null;
}

/** Public URL of a service page. */
function service_url(string $slug): string
{
    return url('services/' . $slug . '.php');
}

/** Public URL of a product page. */
function product_url(string $slug): string
{
    return url('products/' . $slug . '.php');
}

/* =========================================================================
   3. ICONS & ILLUSTRATIONS
   ========================================================================= */

/**
 * Inline SVG icon set (no icon font, no external requests).
 * All icons are 24×24, stroke-based and inherit the current text colour.
 */
function icon(string $name, string $class = 'icon'): string
{
    $paths = [
        'code'      => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
        'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 2.5 15.4 0 18-2.5-2.6-2.5-15.4 0-18z"/>',
        'megaphone' => '<path d="M3 11v2a1 1 0 0 0 1 1h2l4 4V6L6 10H4a1 1 0 0 0-1 1z"/><path d="M14 8a5 5 0 0 1 0 8"/><path d="M17.5 5.5a9 9 0 0 1 0 13"/>',
        'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.6" fill="currentColor" stroke="none"/>',
        'palette'   => '<path d="M12 3a9 9 0 1 0 0 18c1.4 0 2-1 2-2s-.7-2-.7-2.6c0-.8.6-1.4 1.5-1.4H17a4 4 0 0 0 4-4C21 6.3 17 3 12 3z"/><circle cx="8" cy="9" r="1.2" fill="currentColor" stroke="none"/><circle cx="12" cy="7.5" r="1.2" fill="currentColor" stroke="none"/><circle cx="7.5" cy="13.5" r="1.2" fill="currentColor" stroke="none"/>',
        'cloud'     => '<path d="M6.5 18h11a3.5 3.5 0 0 0 .4-6.98A5.5 5.5 0 0 0 7.4 9.2A4 4 0 0 0 6.5 18z"/>',
        'cpu'       => '<rect x="7" y="7" width="10" height="10" rx="2"/><path d="M10 3v3M14 3v3M10 18v3M14 18v3M3 10h3M3 14h3M18 10h3M18 14h3"/>',
        'growth'    => '<polyline points="3 17 9 11 13 15 21 7"/><polyline points="15 7 21 7 21 13"/>',
        'check'     => '<polyline points="20 6 9 17 4 12"/>',
        'arrow'     => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="13 6 19 12 13 18"/>',
        'phone'     => '<path d="M6.6 3h3l1.5 4-2 1.4a12 12 0 0 0 5.5 5.5L16 12l4 1.5v3a2 2 0 0 1-2.2 2A16 16 0 0 1 4.5 5.2 2 2 0 0 1 6.6 3z"/>',
        'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3.5 7 12 13 20.5 7"/>',
        'pin'       => '<path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>',
        'clock'     => '<circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 16 14"/>',
        'whatsapp'  => '<path d="M20 11.5A8.4 8.4 0 0 1 7.7 19L4 20l1-3.6A8.4 8.4 0 1 1 20 11.5z"/><path d="M9 9.5c.4 2.4 2.2 4.2 4.6 4.6l1-1.3 2 .8v1.2c0 .6-.5 1-1.1 1A7 7 0 0 1 8 8.6c0-.6.5-1.1 1.1-1.1h1.2l.8 2z"/>',
        'shield'    => '<path d="M12 3l7 3v5.5c0 4.2-2.9 7.9-7 9.5-4.1-1.6-7-5.3-7-9.5V6l7-3z"/><polyline points="9 12 11.4 14.4 15.5 10"/>',
        'sparkle'   => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z"/>',
        'search'    => '<circle cx="11" cy="11" r="6"/><line x1="15.5" y1="15.5" x2="21" y2="21"/>',
        'chart'     => '<line x1="5" y1="20" x2="5" y2="12"/><line x1="12" y1="20" x2="12" y2="5"/><line x1="19" y1="20" x2="19" y2="9"/>',
        'layers'    => '<polygon points="12 3 21 8 12 13 3 8"/><polyline points="3 13 12 18 21 13"/>',
        'users'     => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0"/><path d="M16 5.6a3.2 3.2 0 0 1 0 6"/><path d="M17.5 14.6A5.5 5.5 0 0 1 20.5 20"/>',
        'menu'      => '<line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>',
        'close'     => '<line x1="6" y1="6" x2="18" y2="18"/><line x1="18" y1="6" x2="6" y2="18"/>',
        'external'  => '<path d="M14 4h6v6"/><path d="M20 4l-8 8"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
        'cart'      => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
        'shopping-bag' => '<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>',
        'heart'     => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
        'tag'       => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
        'star'      => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'truck'     => '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'credit-card' => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'box'       => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        'filter'    => '<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>',
        'trash'     => '<polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
        'plus'      => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'minus'     => '<line x1="5" y1="12" x2="19" y2="12"/>',
        'arrow-left' => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
    ];

    $body = $paths[$name] ?? $paths['check'];

    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
        . 'stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . $body . '</svg>';
}

/**
 * Abstract product/UI mock-up drawn as inline SVG.
 * Used as hero and feature artwork so the site never shows a broken image
 * when a real screenshot or photograph has not been supplied yet.
 */
function mockup(string $variant = 'dashboard', string $class = 'mockup'): string
{
    $frame = '<rect x="0.75" y="0.75" width="518.5" height="358.5" rx="16" fill="url(#pdSurface)" stroke="currentColor" stroke-opacity=".12"/>'
        . '<path d="M1 44h518" stroke="currentColor" stroke-opacity=".12"/>'
        . '<circle cx="26" cy="23" r="5" fill="#FF5F57" fill-opacity=".85"/>'
        . '<circle cx="44" cy="23" r="5" fill="#FEBC2E" fill-opacity=".85"/>'
        . '<circle cx="62" cy="23" r="5" fill="#28C840" fill-opacity=".85"/>'
        . '<rect x="150" y="15" width="220" height="16" rx="8" fill="currentColor" fill-opacity=".08"/>';

    $bodies = [
        // Charts / analytics dashboard
        'analytics' => '<rect x="28" y="66" width="150" height="10" rx="5" fill="currentColor" fill-opacity=".18"/>'
            . '<rect x="28" y="86" width="96" height="8" rx="4" fill="currentColor" fill-opacity=".1"/>'
            . '<rect x="28" y="120" width="220" height="150" rx="12" fill="currentColor" fill-opacity=".05"/>'
            . '<polyline points="48 240 90 200 130 214 170 160 210 176 228 140" fill="none" stroke="url(#pdBrand)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>'
            . '<line x1="48" y1="256" x2="228" y2="256" stroke="currentColor" stroke-opacity=".15"/>'
            . '<rect x="276" y="120" width="104" height="42" rx="10" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="392" y="120" width="104" height="42" rx="10" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="276" y="174" width="104" height="42" rx="10" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="392" y="174" width="104" height="42" rx="10" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="276" y="228" width="220" height="42" rx="10" fill="url(#pdBrand)" fill-opacity=".14"/>'
            . '<rect x="294" y="240" width="70" height="8" rx="4" fill="currentColor" fill-opacity=".22"/>'
            . '<rect x="294" y="254" width="120" height="6" rx="3" fill="currentColor" fill-opacity=".12"/>',
        // Website / browser layout
        'website' => '<rect x="28" y="66" width="240" height="16" rx="8" fill="currentColor" fill-opacity=".16"/>'
            . '<rect x="28" y="94" width="330" height="10" rx="5" fill="currentColor" fill-opacity=".09"/>'
            . '<rect x="28" y="112" width="280" height="10" rx="5" fill="currentColor" fill-opacity=".09"/>'
            . '<rect x="28" y="146" width="120" height="34" rx="17" fill="url(#pdBrand)"/>'
            . '<rect x="160" y="146" width="104" height="34" rx="17" fill="currentColor" fill-opacity=".08"/>'
            . '<rect x="28" y="204" width="146" height="86" rx="12" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="187" y="204" width="146" height="86" rx="12" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="346" y="204" width="146" height="86" rx="12" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="392" y="66" width="100" height="100" rx="14" fill="url(#pdBrand)" fill-opacity=".18"/>',
        // Table / records list (ERP, CRM, billing)
        'table' => '<rect x="28" y="66" width="180" height="12" rx="6" fill="currentColor" fill-opacity=".18"/>'
            . '<rect x="352" y="62" width="140" height="24" rx="12" fill="url(#pdBrand)" fill-opacity=".9"/>'
            . '<rect x="28" y="100" width="464" height="30" rx="8" fill="currentColor" fill-opacity=".10"/>'
            . '<rect x="28" y="138" width="464" height="26" rx="8" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="28" y="172" width="464" height="26" rx="8" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="28" y="206" width="464" height="26" rx="8" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="28" y="240" width="464" height="26" rx="8" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="40" y="108" width="120" height="10" rx="5" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="200" y="108" width="90" height="10" rx="5" fill="currentColor" fill-opacity=".14"/>'
            . '<rect x="330" y="108" width="70" height="10" rx="5" fill="currentColor" fill-opacity=".14"/>'
            . '<rect x="430" y="108" width="50" height="10" rx="5" fill="currentColor" fill-opacity=".14"/>'
            . '<rect x="40" y="146" width="100" height="8" rx="4" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="200" y="146" width="80" height="8" rx="4" fill="currentColor" fill-opacity=".1"/>'
            . '<rect x="40" y="180" width="130" height="8" rx="4" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="200" y="180" width="96" height="8" rx="4" fill="currentColor" fill-opacity=".1"/>'
            . '<rect x="40" y="214" width="110" height="8" rx="4" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="200" y="214" width="70" height="8" rx="4" fill="currentColor" fill-opacity=".1"/>'
            . '<rect x="40" y="248" width="120" height="8" rx="4" fill="currentColor" fill-opacity=".12"/>',
        // Kanban / tasks
        'kanban' => '<rect x="28" y="66" width="140" height="260" rx="12" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="184" y="66" width="140" height="260" rx="12" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="340" y="66" width="152" height="260" rx="12" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="42" y="80" width="80" height="10" rx="5" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="198" y="80" width="80" height="10" rx="5" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="354" y="80" width="80" height="10" rx="5" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="42" y="104" width="112" height="54" rx="9" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="42" y="168" width="112" height="54" rx="9" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="198" y="104" width="112" height="72" rx="9" fill="url(#pdBrand)" fill-opacity=".16"/>'
            . '<rect x="198" y="186" width="112" height="54" rx="9" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="354" y="104" width="124" height="54" rx="9" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="54" y="118" width="72" height="8" rx="4" fill="currentColor" fill-opacity=".18"/>'
            . '<rect x="54" y="134" width="48" height="6" rx="3" fill="currentColor" fill-opacity=".1"/>'
            . '<rect x="210" y="118" width="72" height="8" rx="4" fill="currentColor" fill-opacity=".3"/>'
            . '<rect x="210" y="140" width="60" height="6" rx="3" fill="currentColor" fill-opacity=".16"/>'
            . '<rect x="366" y="118" width="72" height="8" rx="4" fill="currentColor" fill-opacity=".18"/>'
            . '<rect x="366" y="140" width="60" height="6" rx="3" fill="currentColor" fill-opacity=".1"/>'
            . '<rect x="54" y="182" width="64" height="8" rx="4" fill="currentColor" fill-opacity=".14"/>'
            . '<rect x="366" y="182" width="80" height="8" rx="4" fill="currentColor" fill-opacity=".14"/>',
        // Delivery route map
        'map' => '<rect x="28" y="66" width="464" height="260" rx="14" fill="currentColor" fill-opacity=".05"/>'
            . '<path d="M60 280 C130 250 120 160 190 150 S300 210 360 140 430 100 470 110" fill="none" stroke="url(#pdBrand)" stroke-width="5" stroke-linecap="round" stroke-dasharray="1 14"/>'
            . '<path d="M60 280 C130 250 120 160 190 150 S300 210 360 140 430 100 470 110" fill="none" stroke="url(#pdBrand)" stroke-width="5" stroke-linecap="round"/>'
            . '<circle cx="60" cy="280" r="14" fill="url(#pdBrand)"/><circle cx="190" cy="150" r="11" fill="#fff" stroke="url(#pdBrand)" stroke-width="4"/>'
            . '<circle cx="360" cy="140" r="11" fill="#fff" stroke="url(#pdBrand)" stroke-width="4"/><circle cx="470" cy="110" r="14" fill="url(#pdBrand)"/>'
            . '<rect x="300" y="216" width="176" height="86" rx="12" fill="#fff" fill-opacity=".94" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="316" y="234" width="86" height="9" rx="4" fill="currentColor" fill-opacity=".22"/>'
            . '<rect x="316" y="252" width="130" height="7" rx="3" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="316" y="268" width="110" height="7" rx="3" fill="currentColor" fill-opacity=".12"/>',
        // Calendar / queue (hospital, school, appointments)
        'calendar' => '<rect x="28" y="66" width="300" height="260" rx="12" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="46" y="86" width="120" height="10" rx="5" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="344" y="66" width="148" height="52" rx="10" fill="url(#pdBrand)" fill-opacity=".16"/>'
            . '<rect x="344" y="130" width="148" height="52" rx="10" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="344" y="194" width="148" height="52" rx="10" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="344" y="258" width="148" height="68" rx="10" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="360" y="82" width="80" height="8" rx="4" fill="currentColor" fill-opacity=".25"/>'
            . '<rect x="360" y="98" width="110" height="7" rx="3" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="360" y="276" width="90" height="8" rx="4" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="46" y="116" width="80" height="66" rx="10" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="138" y="116" width="80" height="66" rx="10" fill="url(#pdBrand)" fill-opacity=".2"/>'
            . '<rect x="230" y="116" width="80" height="66" rx="10" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="46" y="194" width="80" height="66" rx="10" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="138" y="194" width="80" height="66" rx="10" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="230" y="194" width="80" height="66" rx="10" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="46" y="272" width="80" height="36" rx="10" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>'
            . '<rect x="138" y="272" width="80" height="36" rx="10" fill="#fff" fill-opacity=".9" stroke="currentColor" stroke-opacity=".1"/>',
        // AI / automation nodes
        'ai' => '<circle cx="150" cy="140" r="34" fill="url(#pdBrand)" fill-opacity=".9"/>'
            . '<circle cx="330" cy="106" r="24" fill="#fff" stroke="url(#pdBrand)" stroke-width="4"/>'
            . '<circle cx="380" cy="230" r="24" fill="#fff" stroke="url(#pdBrand)" stroke-width="4"/>'
            . '<circle cx="140" cy="266" r="24" fill="#fff" stroke="url(#pdBrand)" stroke-width="4"/>'
            . '<path d="M182 132 L308 110 M176 162 L362 216 M160 172 L152 244 M322 128 L360 208" stroke="currentColor" stroke-opacity=".28" stroke-width="2" stroke-dasharray="6 6"/>'
            . '<rect x="404" y="70" width="88" height="70" rx="12" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="48" y="170" width="80" height="70" rx="12" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="418" y="84" width="52" height="8" rx="4" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="418" y="100" width="64" height="7" rx="3" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="418" y="116" width="40" height="7" rx="3" fill="currentColor" fill-opacity=".12"/>',
        // Cloud / servers
        'cloudv' => '<path d="M180 150 h160 a56 56 0 0 0 6-111 a76 76 0 0 0-146 22 a50 50 0 0 0-20 89z" fill="url(#pdBrand)" fill-opacity=".18" stroke="url(#pdBrand)" stroke-width="3"/>'
            . '<rect x="86" y="196" width="106" height="120" rx="12" fill="#fff" fill-opacity=".94" stroke="currentColor" stroke-opacity=".12"/>'
            . '<rect x="206" y="196" width="106" height="120" rx="12" fill="#fff" fill-opacity=".94" stroke="currentColor" stroke-opacity=".12"/>'
            . '<rect x="326" y="196" width="106" height="120" rx="12" fill="#fff" fill-opacity=".94" stroke="currentColor" stroke-opacity=".12"/>'
            . '<rect x="102" y="214" width="60" height="8" rx="4" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="222" y="214" width="60" height="8" rx="4" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="342" y="214" width="60" height="8" rx="4" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="102" y="234" width="74" height="7" rx="3" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="222" y="234" width="74" height="7" rx="3" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="342" y="234" width="74" height="7" rx="3" fill="currentColor" fill-opacity=".12"/>'
            . '<rect x="102" y="272" width="34" height="8" rx="4" fill="#28C840" fill-opacity=".7"/>'
            . '<rect x="222" y="272" width="34" height="8" rx="4" fill="#28C840" fill-opacity=".7"/>'
            . '<rect x="342" y="272" width="34" height="8" rx="4" fill="#FEBC2E" fill-opacity=".8"/>',
        // Funnel / growth steps
        'funnel' => '<polygon points="70 76 450 76 370 168 150 168" fill="url(#pdBrand)" fill-opacity=".9"/>'
            . '<polygon points="158 184 362 184 320 246 200 246" fill="url(#pdBrand)" fill-opacity=".55"/>'
            . '<polygon points="206 262 314 262 288 306 232 306" fill="url(#pdBrand)" fill-opacity=".3"/>'
            . '<rect x="70" y="96" width="90" height="10" rx="5" fill="#fff" fill-opacity=".5"/>'
            . '<rect x="176" y="200" width="70" height="8" rx="4" fill="currentColor" fill-opacity=".25"/>'
            . '<rect x="222" y="276" width="60" height="8" rx="4" fill="currentColor" fill-opacity=".25"/>',
        // Brand / creative board
        'brand' => '<rect x="48" y="80" width="140" height="140" rx="16" fill="url(#pdBrand)"/>'
            . '<rect x="204" y="80" width="140" height="66" rx="14" fill="currentColor" fill-opacity=".1"/>'
            . '<rect x="204" y="154" width="140" height="66" rx="14" fill="currentColor" fill-opacity=".06"/>'
            . '<rect x="360" y="80" width="132" height="140" rx="14" fill="currentColor" fill-opacity=".08"/>'
            . '<circle cx="118" cy="128" r="26" fill="#fff" fill-opacity=".85"/>'
            . '<rect x="88" y="176" width="60" height="12" rx="6" fill="#fff" fill-opacity=".7"/>'
            . '<rect x="220" y="98" width="86" height="12" rx="6" fill="currentColor" fill-opacity=".28"/>'
            . '<rect x="220" y="120" width="60" height="8" rx="4" fill="currentColor" fill-opacity=".14"/>'
            . '<rect x="376" y="98" width="80" height="90" rx="8" fill="#fff" fill-opacity=".8"/>'
            . '<rect x="48" y="240" width="444" height="66" rx="14" fill="currentColor" fill-opacity=".05"/>'
            . '<rect x="66" y="258" width="120" height="10" rx="5" fill="currentColor" fill-opacity=".2"/>'
            . '<rect x="66" y="278" width="200" height="8" rx="4" fill="currentColor" fill-opacity=".12"/>',
    ];

    $body = $bodies[$variant] ?? $bodies['analytics'];

    // Several mock-ups can appear on one page, so every gradient needs its own
    // id — duplicate ids are invalid HTML and break the fill references.
    $seq        = (int) ($GLOBALS['_pd_mockup_seq'] = (int) ($GLOBALS['_pd_mockup_seq'] ?? 0) + 1);
    $brand_id   = 'pdBrand' . $seq;
    $surface_id = 'pdSurface' . $seq;

    $body  = str_replace(['url(#pdBrand)', 'url(#pdSurface)'], ['url(#' . $brand_id . ')', 'url(#' . $surface_id . ')'], $body);
    $frame = str_replace(['url(#pdBrand)', 'url(#pdSurface)'], ['url(#' . $brand_id . ')', 'url(#' . $surface_id . ')'], $frame);

    return '<svg class="' . e($class) . '" viewBox="0 0 520 360" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">'
        . '<defs>'
        . '<linearGradient id="' . e($brand_id) . '" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0%" stop-color="#1B4DFF"/><stop offset="100%" stop-color="#7C5CFF"/></linearGradient>'
        . '<linearGradient id="' . e($surface_id) . '" x1="0" y1="0" x2="0" y2="1">'
        . '<stop offset="0%" stop-color="#ffffff"/><stop offset="100%" stop-color="#F7F8FC"/></linearGradient>'
        . '</defs>'
        . $frame . $body . '</svg>';
}

/**
 * Hero artwork for a page.
 * Looks for a real image first (webp → jpg → png), then falls back to the
 * bundled SVG placeholder in /assets/hero/, and finally to an inline mock-up.
 * Drop a same-named .webp file into /assets/hero/ and it is used automatically.
 */
function hero_image(string $slug, string $alt, string $class = 'hero-visual__img'): string
{
    $candidates = [
        'hero/' . $slug . '.webp',
        'hero/' . $slug . '.jpg',
        'hero/' . $slug . '.jpeg',
        'hero/' . $slug . '.png',
        'hero/' . $slug . '.svg',
        'images/services/' . $slug . '.jpg',
        'images/services/' . $slug . '.webp',
        'images/services/' . $slug . '.png',
    ];

    foreach ($candidates as $candidate) {
        if (is_file(dirname(__DIR__) . '/assets/' . $candidate)) {
            return '<img class="' . e($class) . '" src="' . e(asset($candidate)) . '" alt="' . e($alt)
                . '" width="1200" height="760" loading="lazy" decoding="async">';
        }
    }

    return mockup('dashboard', 'mockup mockup--hero');
}

/* =========================================================================
   4. CSRF PROTECTION
   ========================================================================= */

/** One CSRF token per session, generated from a random 32-byte value. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Hidden input to place inside every state-changing form. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Constant-time comparison; false when the token is missing or wrong. */
function csrf_valid(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/* =========================================================================
   5. FORM INPUT HELPERS
   ========================================================================= */

/** Read a POST value as a trimmed, control-character-free string. */
function post_string(string $key, int $max = 2000): string
{
    $value = $_POST[$key] ?? '';
    if (!is_string($value)) {
        return '';
    }
    $value = str_replace(["\r\n", "\r"], "\n", $value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
    return mb_substr(trim($value), 0, $max);
}

/** Keep a submitted value in the form after a validation error. */
function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

function valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL) && mb_strlen($email) <= 190;
}

/** Accepts +91 7000-12-7225, 07000127225, +91 (0) 70001 27225 … */
function valid_phone(string $phone): bool
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    return strlen($digits) >= 7 && strlen($digits) <= 16;
}

/** Remove HTML and collapse whitespace before anything is stored. */
function clean_text(string $value, int $max = 5000): string
{
    $value = strip_tags($value);
    $value = preg_replace('/[ \t]+/', ' ', $value) ?? $value;
    return mb_substr(trim($value), 0, $max);
}

/** Block header-injection attempts in values used inside an e-mail header. */
function header_safe(string $value): string
{
    return trim(str_replace(["\r", "\n", "%0a", "%0d"], '', $value));
}

/* =========================================================================
   6. FLASH MESSAGES (post-redirect-get)
   ========================================================================= */

function set_flash(string $type, string $message, array $extra = []): void
{
    $_SESSION['flash'] = array_merge(['type' => $type, 'message' => $message], $extra);
}

function take_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/* =========================================================================
   7. CONTACT FORM PROCESSING
   ========================================================================= */

/**
 * Validate and process the contact form.
 *
 * Returns:
 *   ['ok' => bool, 'errors' => string[], 'field_errors' => array<string,string>,
 *    'general_errors' => string[], 'values' => array, 'reference' => ?string]
 *
 * $field_errors maps a form field name to its message, which lets the form mark
 * the exact input with aria-invalid and print the message beside it.
 * $general_errors holds session / spam / rate-limit messages shown in a summary.
 *
 * On success the enquiry is stored as JSON lines in /data/enquiries/ (always)
 * and, only when $CONTACT_CONFIG['driver'] is 'mail', an e-mail is attempted.
 */
function process_enquiry(): array
{
    global $CONTACT_CONFIG;

    $field_errors   = [];
    $general_errors = [];
    $values = [
        'name'    => post_string('name', 120),
        'email'   => post_string('email', 190),
        'phone'   => post_string('phone', 40),
        'company' => post_string('company', 160),
        'service' => post_string('service', 120),
        'budget'  => post_string('budget', 60),
        'message' => post_string('message', 4000),
    ];
    $consent = isset($_POST['consent']);

    // ---- CSRF -------------------------------------------------------------
    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        $general_errors[] = 'Your session expired before the form was submitted. Please review the form and try again.';
    }

    // ---- Spam protection --------------------------------------------------
    $honeypot = post_string('website_url', 200);          // hidden field
    if ($honeypot !== '') {
        $general_errors[] = 'The form could not be submitted. Please contact us by phone or e-mail.';
    }

    $started = isset($_POST['form_started']) ? (int) $_POST['form_started'] : 0;
    $elapsed = $started > 0 ? (time() - $started) : 0;
    $min_sec = (int) ($CONTACT_CONFIG['min_seconds_on_form'] ?? 0);
    if ($min_sec > 0 && $started > 0 && $elapsed >= 0 && $elapsed < $min_sec) {
        $general_errors[] = 'That was a little too quick — please take a moment and submit again.';
    }

    // ---- Rate limit -------------------------------------------------------
    $now = time();
    $window = (int) ($CONTACT_CONFIG['session_window'] ?? 300);
    $hits = $_SESSION['enquiry_times'] ?? [];
    $hits = array_values(array_filter($hits, static fn ($t) => ($now - (int) $t) < $window));
    if (count($hits) >= (int) ($CONTACT_CONFIG['max_per_session'] ?? 30)) {
        $general_errors[] = 'We have already received several enquiries from this browser. Please call or WhatsApp us instead.';
    }

    // ---- Field rules (each message is tied to its input) ------------------
    if ($values['name'] === '') {
        $field_errors['name'] = 'Please enter your name.';
    }

    if ($values['email'] === '') {
        $field_errors['email'] = 'Please enter your e-mail address.';
    } elseif (!valid_email($values['email'])) {
        $field_errors['email'] = 'That e-mail address does not look valid. Please check it.';
    }

    if ($values['phone'] === '') {
        $field_errors['phone'] = 'Please enter a phone number so we can reach you.';
    } elseif (!valid_phone($values['phone'])) {
        $field_errors['phone'] = 'Please enter a valid phone number (7–16 digits).';
    }

    if ($values['service'] === '') {
        $values['service'] = 'General Enquiry';
    }

    if ($values['message'] === '') {
        $field_errors['message'] = 'Please tell us briefly what you need help with.';
    }

    if (!$consent) {
        $consent = true;
    }

    $errors = array_merge(array_values($general_errors), array_values($field_errors));

    // ---- Store ------------------------------------------------------------
    if (!$errors) {
        $reference = 'PD-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));

        $record = [
            'reference'   => $reference,
            'received_at' => date('c'),
            'name'        => clean_text($values['name'], 120),
            'email'       => clean_text($values['email'], 190),
            'phone'       => clean_text($values['phone'], 40),
            'company'     => clean_text($values['company'], 160),
            'service'     => clean_text($values['service'], 120),
            'budget'      => clean_text($values['budget'], 60),
            'message'     => clean_text($values['message'], 4000),
            'source_page' => clean_text($_SERVER['HTTP_REFERER'] ?? 'direct', 300),
            'ip_hash'     => hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|prospect-digital'),
            'user_agent'  => clean_text($_SERVER['HTTP_USER_AGENT'] ?? '', 300),
            'mail_sent'   => false,
        ];

        $stored = store_enquiry($record);

        if ($stored && in_array($CONTACT_CONFIG['driver'], ['mail', 'smtp'], true)) {
            $record['mail_sent'] = send_enquiry_mail($record);
        }

        $hits[] = $now;
        $_SESSION['enquiry_times'] = $hits;

        set_flash('success', 'Thank you — your enquiry has been received.', [
            'reference' => $reference,
            'mail_sent' => !empty($record['mail_sent']),
            'stored'    => $stored,
        ]);
    }

    return [
        'ok'             => !$errors,
        'errors'         => $errors,
        'field_errors'   => $field_errors,
        'general_errors' => $general_errors,
        'values'         => $values,
        'reference'      => $reference ?? null,
    ];
}

/** Append one enquiry as a JSON line. Creates the folder on first use. */
function store_enquiry(array $record): bool
{
    global $pdo;

    $database_stored = false;
    $file_stored     = false;

    /* ---------------------------------------------------------------
       1. Store in MySQL
       --------------------------------------------------------------- */
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO enquiries
                    (reference, name, email, phone, company, service, budget, message)
                VALUES
                    (:reference, :name, :email, :phone, :company, :service, :budget, :message)
            ");

            $stmt->execute([
                ':reference' => $record['reference'] ?? null,
                ':name'      => $record['name'],
                ':email'     => $record['email'],
                ':phone'     => $record['phone'],
                ':company'   => $record['company'],
                ':service'   => $record['service'],
                ':budget'    => $record['budget'],
                ':message'   => $record['message'],
            ]);

            $database_stored = true;

        } catch (PDOException $e) {
            error_log(
                'Prospect Digital: MySQL enquiry insert failed: '
                . $e->getMessage()
            );
        }
    }

    /* ---------------------------------------------------------------
       2. Keep JSONL backup
       --------------------------------------------------------------- */
    if (!is_dir(ENQUIRY_DIR)) {
        if (!@mkdir(ENQUIRY_DIR, 0775, true) && !is_dir(ENQUIRY_DIR)) {
            error_log(
                'Prospect Digital: could not create '
                . ENQUIRY_DIR
            );

            return $database_stored;
        }
    }

    $line = json_encode(
        $record,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );

    if ($line !== false) {
        $written = @file_put_contents(
            ENQUIRY_DIR
            . DIRECTORY_SEPARATOR
            . 'enquiries-' . date('Y-m') . '.jsonl',
            $line . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
        if ($written !== false) {
            $file_stored = true;
        }
    }

    return $database_stored || $file_stored;
}

/**
 * Send an email directly via authenticated SMTP socket (pure PHP, zero dependencies).
 * Works reliably on Hostinger (smtp.hostinger.com, port 465 SSL or 587 TLS).
 */
function pd_send_smtp_mail(array $smtp, string $to, string $subject, string $body, array $headers = []): bool
{
    $host       = $smtp['host'] ?? 'smtp.hostinger.com';
    $port       = (int) ($smtp['port'] ?? 465);
    $user       = $smtp['username'] ?? '';
    $pass       = $smtp['password'] ?? '';
    $encryption = strtolower((string) ($smtp['encryption'] ?? 'ssl'));
    $timeout    = (int) ($smtp['timeout'] ?? 10);

    if (empty($host) || empty($user) || empty($pass)) {
        error_log('Prospect Digital SMTP: Missing host, username, or password in configuration.');
        return false;
    }

    $socketHost = ($encryption === 'ssl') ? 'ssl://' . $host : $host;
    $socket = @fsockopen($socketHost, $port, $errno, $errstr, $timeout);
    if (!$socket) {
        error_log("Prospect Digital SMTP: Connection to {$host}:{$port} failed: {$errstr} ({$errno})");
        return false;
    }

    stream_set_timeout($socket, $timeout);

    $readResponse = static function () use ($socket): string {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    };

    $sendCommand = static function (string $cmd) use ($socket): void {
        fputs($socket, $cmd . "\r\n");
    };

    $checkCode = static function (string $response, string $expectedCode): bool {
        return str_starts_with(trim($response), $expectedCode);
    };

    $res = $readResponse();
    if (!$checkCode($res, '220')) {
        error_log("Prospect Digital SMTP: Invalid greeting: {$res}");
        fclose($socket);
        return false;
    }

    $clientDomain = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $sendCommand("EHLO {$clientDomain}");
    $res = $readResponse();

    if ($encryption === 'tls') {
        $sendCommand('STARTTLS');
        $res = $readResponse();
        if (!$checkCode($res, '220')) {
            error_log("Prospect Digital SMTP: STARTTLS command failed: {$res}");
            fclose($socket);
            return false;
        }
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log('Prospect Digital SMTP: TLS cryptographic handshake failed.');
            fclose($socket);
            return false;
        }
        $sendCommand("EHLO {$clientDomain}");
        $res = $readResponse();
    }

    // AUTH LOGIN
    $sendCommand('AUTH LOGIN');
    $res = $readResponse();
    if (!$checkCode($res, '334')) {
        error_log("Prospect Digital SMTP: AUTH LOGIN failed: {$res}");
        fclose($socket);
        return false;
    }

    $sendCommand(base64_encode($user));
    $res = $readResponse();
    if (!$checkCode($res, '334')) {
        error_log("Prospect Digital SMTP: Username rejected: {$res}");
        fclose($socket);
        return false;
    }

    $sendCommand(base64_encode($pass));
    $res = $readResponse();
    if (!$checkCode($res, '235')) {
        error_log("Prospect Digital SMTP: Authentication failed: {$res}");
        fclose($socket);
        return false;
    }

    // Envelope
    $fromEmail = !empty($smtp['from_email']) ? $smtp['from_email'] : $user;
    $sendCommand("MAIL FROM: <{$fromEmail}>");
    $res = $readResponse();
    if (!$checkCode($res, '250')) {
        error_log("Prospect Digital SMTP: MAIL FROM rejected: {$res}");
        fclose($socket);
        return false;
    }

    $sendCommand("RCPT TO: <{$to}>");
    $res = $readResponse();
    if (!$checkCode($res, '250')) {
        error_log("Prospect Digital SMTP: RCPT TO rejected: {$res}");
        fclose($socket);
        return false;
    }

    $sendCommand('DATA');
    $res = $readResponse();
    if (!$checkCode($res, '354')) {
        error_log("Prospect Digital SMTP: DATA command rejected: {$res}");
        fclose($socket);
        return false;
    }

    // Build message
    $headers['To']      = $to;
    $headers['Subject'] = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers['Date']    = date('r');

    $headerStr = '';
    foreach ($headers as $k => $v) {
        $headerStr .= "{$k}: {$v}\r\n";
    }

    $msg = $headerStr . "\r\n" . $body . "\r\n.";
    $sendCommand($msg);
    $res = $readResponse();
    $sent = $checkCode($res, '250');

    $sendCommand('QUIT');
    fclose($socket);

    return $sent;
}

/**
 * Dispatch an enquiry notification email using configured driver (smtp or mail).
 */
function send_enquiry_mail(array $record): bool
{
    global $CONTACT_CONFIG;

    $driver  = $CONTACT_CONFIG['driver'] ?? 'log';
    $to      = header_safe($CONTACT_CONFIG['to_email']);
    $from    = header_safe($CONTACT_CONFIG['from_email']);
    $subject = header_safe($CONTACT_CONFIG['subject_prefix'] . ' ' . $record['service'] . ' — ' . $record['name']);

    $lines = [
        'Reference: ' . $record['reference'],
        'Received : ' . $record['received_at'],
        'Name     : ' . $record['name'],
        'E-mail   : ' . $record['email'],
        'Phone    : ' . $record['phone'],
        'Company  : ' . $record['company'],
        'Service  : ' . $record['service'],
        'Budget   : ' . $record['budget'],
        '',
        'Message:',
        $record['message'],
    ];
    $body = implode("\n", $lines);

    $headers = [
        'From'         => header_safe($CONTACT_CONFIG['to_name']) . ' <' . $from . '>',
        'Reply-To'     => header_safe($record['name']) . ' <' . header_safe($record['email']) . '>',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'X-Mailer'     => 'Prospect Digital / PHP ' . PHP_VERSION,
    ];

    if ($driver === 'smtp') {
        $smtpConfig = $CONTACT_CONFIG['smtp'] ?? [];
        $smtpConfig['from_email'] = $from;
        return pd_send_smtp_mail($smtpConfig, $to, $subject, $body, $headers);
    }

    if ($driver === 'mail') {
        $rawHeaders = [];
        foreach ($headers as $k => $v) {
            $rawHeaders[] = "{$k}: {$v}";
        }
        return @mail($to, $subject, $body, implode("\r\n", $rawHeaders), "-f " . escapeshellarg($from));
    }

    return false;
}

/* =========================================================================
   8. SMALL VIEW HELPERS
   ========================================================================= */

/** Render the reusable section heading block (eyebrow + title + description). */
function section_head(string $eyebrow, string $title, string $description = '', string $align = 'left', string $level = 'h2'): void
{
    $tag = in_array($level, ['h1', 'h2', 'h3'], true) ? $level : 'h2';
    ?>
    <header class="section-head section-head--<?= e($align) ?>" data-reveal>
        <?php if ($eyebrow !== ''): ?>
            <p class="eyebrow"><?= e($eyebrow) ?></p>
        <?php endif; ?>
        <<?= $tag ?> class="section-head__title"><?= e($title) ?></<?= $tag ?>>
        <?php if ($description !== ''): ?>
            <p class="section-head__text"><?= e($description) ?></p>
        <?php endif; ?>
    </header>
    <?php
}

/** Options for the "service interested in" dropdown. */
function service_options(): array
{
    $options = [];
    foreach (all_services() as $service) {
        $options[$service['name']] = $service['name'];
    }
    $options['Product enquiry'] = 'Product enquiry (RouteFlow / Workora / Bizora / Medvora / Schova)';
    $options['Not sure yet'] = 'Not sure yet — need advice';
    return $options;
}

/** Budget bands shown in the contact form. */
function budget_options(): array
{
    return [
        'Under ₹50,000',
        '₹50,000 – ₹1,50,000',
        '₹1,50,000 – ₹5,00,000',
        'Above ₹5,00,000',
        'Monthly retainer',
        'To be discussed',
    ];
}

