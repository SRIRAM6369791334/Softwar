<?php
// Phase 11: Add region to branches
require __DIR__ . '/config/database.php';
$config = require __DIR__ . '/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected\n";
    $pdo->exec("ALTER TABLE branches ADD COLUMN region VARCHAR(50) DEFAULT 'General' AFTER location");
    echo "✓ Added region to branches\n";

    // Set some defaults for the map visualization
    $pdo->exec("UPDATE branches SET region = 'North' WHERE id = 1");
    
} catch (Exception $e) {
    echo "✖ Error: " . $e->getMessage() . "\n";
}
