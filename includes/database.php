<?php

declare(strict_types=1);

$db_host = '127.0.0.1';
$db_name = 'prospect_digital';
$db_user = 'root';
$db_pass = '';

try {

    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

} catch (PDOException $e) {

    error_log('Database connection failed: ' . $e->getMessage());

    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        die('Database connection failed.');
    }

    http_response_code(500);
    die('Something went wrong. Please try again later.');
}