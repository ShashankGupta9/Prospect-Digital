<?php
/**
 * Prospect Digital — development router for PHP's built-in server
 * ---------------------------------------------------------------------------
 * FOR LOCAL DEVELOPMENT ONLY. Apache does not need this file; the shipped
 * .htaccess performs the same two jobs (pretty URLs and /sitemap.xml).
 *
 * Usage (from inside the project folder):
 *     php -S localhost:8000 router.php
 *
 * It lets you open:
 *     http://localhost:8000/services/software-development
 *     http://localhost:8000/sitemap.xml
 * exactly as they would behave on Apache hosting.
 * ---------------------------------------------------------------------------
 */

$root = __DIR__;
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = rtrim($root . $uri, '/');

// 1. Existing files (assets, images, robots.txt, PHP pages) are served directly.
if ($uri !== '/' && is_file($root . $uri)) {
    return false;
}

// 2. /sitemap.xml → sitemap.xml.php
if ($uri === '/sitemap.xml') {
    require $root . '/sitemap.xml.php';
    return true;
}

// 3. Directory index
if (is_dir($path) && is_file($path . '/index.php')) {
    require $path . '/index.php';
    return true;
}

// 4. Extensionless page URLs → .php
if (is_file($path . '.php')) {
    require $path . '.php';
    return true;
}

// 5. Anything else: the 404 page, with the correct status code.
require $root . '/404.php';
return true;
