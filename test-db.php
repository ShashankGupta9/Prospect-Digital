<?php

require_once __DIR__ . '/includes/config.php';

try {
    $pdo->query("SELECT 1");
    echo "Database connected successfully!";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}