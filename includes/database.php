<?php

declare(strict_types=1);

$db_host    = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
$db_name    = defined('DB_NAME') ? DB_NAME : 'prospect_digital';
$db_user    = defined('DB_USER') ? DB_USER : 'root';
$db_pass    = defined('DB_PASS') ? DB_PASS : '';
$db_port    = defined('DB_PORT') ? (int) DB_PORT : 3306;
$db_charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';

$pdo = null;
$GLOBALS['db_connection_error'] = null;

try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset={$db_charset}";

    $pdo = new PDO(
        $dsn,
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ]
    );

} catch (PDOException $e) {
    $pdo = null;
    $GLOBALS['db_connection_error'] = $e->getMessage();
    error_log('Prospect Digital: Database connection failed: ' . $e->getMessage());
}

/**
 * Returns the active PDO connection or null if connection failed.
 */
function db(): ?PDO
{
    global $pdo;
    return $pdo instanceof PDO ? $pdo : null;
}

/**
 * Checks whether MySQL database connection is currently active.
 */
function db_is_connected(): bool
{
    global $pdo;
    return $pdo instanceof PDO;
}