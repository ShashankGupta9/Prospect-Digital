<?php
/**
 * Prospect Digital — central configuration
 * ---------------------------------------------------------------------------
 * This is the ONLY file you normally need to edit to change company details,
 * the site URL, contact-form behaviour or e-mail settings.
 *
 * Nothing secret should ever be written inside the public HTML pages.
 * Keep credentials / SMTP passwords in this file (or in an environment
 * variable) — never in a page, script or template that is served as text.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// 1. Environment
// ---------------------------------------------------------------------------
// DEBUG_MODE = true while developing (shows PHP errors).
// Set it to false on the live server (errors are logged, never printed).
define('DEBUG_MODE', true);

// ---------------------------------------------------------------------------
// 2. Website address
// ---------------------------------------------------------------------------
// Canonical / Open Graph / sitemap URLs are built from this value.
// Change it to your real domain before going live.
define('SITE_URL', 'https://prospectdigital.in');

// Local preview helper: when the site is opened on localhost the canonical
// URL stays SITE_URL (correct for production), but links keep working
// because every link is generated relative to the installed folder.
define('ASSET_VERSION', '1.0.0');

date_default_timezone_set('Asia/Kolkata');

// ---------------------------------------------------------------------------
// 3. Company details (single source of truth for the whole website)
// ---------------------------------------------------------------------------
define('COMPANY_NAME',      'Prospect Digital');
define('COMPANY_TAGLINE',   'Build. Grow. Scale.');
define('COMPANY_DESCRIPTION', 'We build digital solutions that deliver business growth — websites, software, cloud and marketing that make a positive impact.');

define('COMPANY_ADDRESS',   'R-52, First Floor, Gulab Vila, near Hotel Shree Vatika &amp; Chetak Bridge, Zone-1, M.P. Nagar, Bhopal, Madhya Pradesh 462011');
define('COMPANY_ADDRESS_LINE_1', 'R-52, First Floor, Gulab Vila,');
define('COMPANY_ADDRESS_LINE_2', 'near Hotel Shree Vatika &amp; Chetak Bridge,');
define('COMPANY_ADDRESS_LINE_3', 'Zone-1, M.P. Nagar, Bhopal,');
define('COMPANY_ADDRESS_LINE_4', 'Madhya Pradesh 462011');
define('COMPANY_CITY',      'Bhopal');
define('COMPANY_STATE',     'Madhya Pradesh');
define('COMPANY_COUNTRY',   'India');
define('COMPANY_PINCODE',   '462011');

define('COMPANY_EMAIL',     'hello@prospectdigital.in');
define('COMPANY_PHONE_DISPLAY', '+91 7000-12-7225');
define('COMPANY_PHONE_RAW', '917000127225');           // digits only
define('COMPANY_PHONE_URL', 'tel:917000127225');
define('COMPANY_WHATSAPP_URL', 'https://wa.me/917000127225?text=Hello%20Prospect%20Digital%20%F0%9F%91%8B');
define('COMPANY_EMAIL_URL', 'mailto:hello@prospectdigital.in');
define('COMPANY_HOURS',     'Mon–Sat 10:00 AM – 7:00 PM IST');

// Geo coordinates used in structured data (Bhopal, M.P. Nagar).
define('COMPANY_LATITUDE',  '23.2330');
define('COMPANY_LONGITUDE', '77.4345');

// ---------------------------------------------------------------------------
// 4. Contact form
// ---------------------------------------------------------------------------
$CONTACT_CONFIG = [
    // Where an enquiry copy should go once mail delivery is configured.
    'to_email'   => 'hello@prospectdigital.in',
    'to_name'    => 'Prospect Digital Enquiries',
    'from_email' => 'no-reply@prospectdigital.in',

    /*
     * MAIL DRIVER
     * -----------
     * 'log'  → enquiries are validated and stored in /data/enquiries/ only.
     *          The visitor still sees a success message with a reference ID.
     *          USE THIS UNTIL SMTP/MAIL IS CONFIGURED.
     * 'mail' → additionally attempts PHP's mail() function (works on servers
     *          with a configured MTA; often blocked on shared hosting).
     * 'smtp' → reserved for a real SMTP library (PHPMailer / Symfony Mailer).
     *          Not bundled with this project on purpose: no third-party code
     *          is shipped here, so 'smtp' currently behaves like 'log'.
     *
     * IMPORTANT: this project does NOT claim e-mails are sent. While the
     * driver is 'log', the site honestly tells the visitor that the enquiry
     * has been recorded and shows the direct phone / WhatsApp options.
     */
    'driver'     => 'log',

    // Used only when driver = 'mail'.
    'subject_prefix' => '[Website enquiry]',

    // Basic spam protection.
    'min_seconds_on_form' => 0,      // 0 to avoid false positive blocks for human visitors
    'max_per_session'     => 30,     // allow testing and repeat inquiries
    'session_window'      => 300,    // seconds for the rate limit window
];

// ---------------------------------------------------------------------------
// 5. Storage
// ---------------------------------------------------------------------------
// Enquiries are appended as one JSON object per line to this folder.
// The folder ships with an .htaccess file that blocks direct web access.
define('DATA_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data');
define('ENQUIRY_DIR', DATA_DIR . DIRECTORY_SEPARATOR . 'enquiries');

// ---------------------------------------------------------------------------
// 6. Base URL detection (lets the site work at /  or  /prospect-digital/)
// ---------------------------------------------------------------------------
/**
 * Work out the web path the project is installed under, e.g. '' when the
 * project is the document root, or '/prospect-digital' inside XAMPP htdocs.
 * No hard-coded absolute filesystem paths are used anywhere.
 */
function pd_detect_base_url(): string
{
    $root = str_replace('\\', '/', dirname(__DIR__));                 // project root
    $doc  = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : '';
    $doc  = $doc ? str_replace('\\', '/', $doc) : '';
    $realRoot = realpath($root);
    $realRoot = $realRoot ? str_replace('\\', '/', $realRoot) : $root;

    $base = '';
    if ($doc !== '' && str_starts_with($realRoot, $doc)) {
        $base = substr($realRoot, strlen($doc));
    }
    $base = '/' . trim((string) $base, '/');
    return $base === '/' ? '' : $base;
}

define('BASE_URL', pd_detect_base_url());

// ---------------------------------------------------------------------------
// 7. Errors & session
// ---------------------------------------------------------------------------
if (DEBUG_MODE) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('log_errors', '1');
}

if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => BASE_URL === '' ? '/' : BASE_URL . '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => $secure,
    ]);
    session_start();
}


// ---------------------------------------------------------------------------
// 8. Shared libraries
// ---------------------------------------------------------------------------
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/user-auth.php';
