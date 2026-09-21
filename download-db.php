<?php
/**
 * Prospect Digital — Database Extract & Direct Download Tool
 * ---------------------------------------------------------------------------
 * Visiting this script in your browser automatically downloads the complete
 * MySQL database extract (database.sql) ready for Hostinger phpMyAdmin.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

$file = __DIR__ . '/database.sql';

if (!file_exists($file)) {
    http_response_code(404);
    die('Database extract file (database.sql) not found.');
}

$filename = 'prospect_digital_database_' . date('Y-m-d') . '.sql';

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . (string) filesize($file));

// Clear output buffers
if (ob_get_level()) {
    ob_end_clean();
}

readfile($file);
exit;
