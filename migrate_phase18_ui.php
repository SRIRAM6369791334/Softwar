<?php
// Phase 18.1: UI Enhancements - Custom Backgrounds
require __DIR__ . '/public/index.php'; // Boot the app to get DB connection

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "Migration: Adding background_url to branches table...\n";
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM branches LIKE 'background_url'");
    if ($stmt->fetch()) {
        echo "Column 'background_url' already exists.\n";
    } else {
        $pdo->exec("ALTER TABLE branches ADD COLUMN background_url VARCHAR(255) NULL AFTER location");
        echo "✓ Added 'background_url' column to branches table.\n";
    }

    echo "Migration complete!\n";

} catch (Exception $e) {
    echo "✖ Error: " . $e->getMessage() . "\n";
    exit(1);
}
