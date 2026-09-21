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
// 1. Environment & .env Configuration
// ---------------------------------------------------------------------------
// Load optional .env file if present in the project root
$env_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
if (is_file($env_path) && is_readable($env_path)) {
    $env_lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($env_lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($k, $_SERVER) && !array_key_exists($k, $_ENV)) {
                putenv("{$k}={$v}");
                $_ENV[$k] = $v;
                $_SERVER[$k] = $v;
            }
        }
    }
}

// Auto-detect local development vs live production server
$is_local = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
    || str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost')
    || str_contains($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1');

// DEBUG_MODE = true while developing (shows PHP errors).
// Set it to false on the live server (errors are logged, never printed).
$debug_env = getenv('APP_DEBUG');
define('DEBUG_MODE', $debug_env !== false ? in_array(strtolower((string)$debug_env), ['1', 'true', 'yes'], true) : false);

// ---------------------------------------------------------------------------
// 2. Website address & Database Configuration
// ---------------------------------------------------------------------------
// Canonical / Open Graph / sitemap URLs are built from this value.
define('SITE_URL', getenv('SITE_URL') ?: 'https://prospectdigital.in');
define('ASSET_VERSION', '1.0.0');

date_default_timezone_set('Asia/Kolkata');

// Database credentials (MySQL / MariaDB on Hostinger or local XAMPP)
if (!defined('DB_HOST'))    define('DB_HOST',    getenv('DB_HOST') ?: '127.0.0.1');
if (!defined('DB_NAME'))    define('DB_NAME',    getenv('DB_NAME') ?: 'prospect_digital');
if (!defined('DB_USER'))    define('DB_USER',    getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS'))    define('DB_PASS',    getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
if (!defined('DB_PORT'))    define('DB_PORT',    (int) (getenv('DB_PORT') ?: 3306));
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

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
// 4. Contact form & Mailer Settings (Hostinger / Custom SMTP)
// ---------------------------------------------------------------------------
$CONTACT_CONFIG = [
    // Where an enquiry copy should go once mail delivery is configured.
    'to_email'   => getenv('MAIL_TO') ?: 'hello@prospectdigital.in',
    'to_name'    => 'Prospect Digital Enquiries',
    'from_email' => getenv('MAIL_FROM') ?: 'hello@prospectdigital.in',

    /*
     * MAIL DRIVER
     * -----------
     * 'log'  → enquiries are validated and stored in /data/enquiries/ and MySQL.
     *          The visitor sees a confirmation with reference ID.
     * 'mail' → additionally attempts PHP's standard mail() function.
     * 'smtp' → delivers directly via authenticated SMTP (Hostinger / Titan / Google).
     */
    'driver'     => getenv('MAIL_DRIVER') ?: 'log',

    // Used when sending emails.
    'subject_prefix' => '[Website enquiry]',

    // SMTP Settings (for Hostinger or external SMTP)
    'smtp' => [
        'host'       => getenv('SMTP_HOST') ?: 'smtp.hostinger.com',
        'port'       => (int) (getenv('SMTP_PORT') ?: 465),
        'username'   => getenv('SMTP_USER') ?: 'hello@prospectdigital.in',
        'password'   => getenv('SMTP_PASS') !== false ? getenv('SMTP_PASS') : '',
        'encryption' => getenv('SMTP_ENCRYPTION') ?: 'ssl', // 'ssl' (port 465) or 'tls' (port 587)
        'timeout'    => 10,
    ],

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
