<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Checking Schema for Phase 24...\n\n";

$tablesToCheck = ['products', 'categories', 'users', 'invoices'];
foreach ($tablesToCheck as $table) {
    echo "Checking table '$table': ";
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('deleted_at', $columns)) {
            echo "✅ 'deleted_at' exists.\n";
        } else {
            echo "❌ 'deleted_at' MISSING.\n";
        }
    } catch (Exception $e) {
        echo "❌ Table not found.\n";
    }
}

echo "\nChecking 'archives' table: ";
try {
    $pdo->query("SELECT 1 FROM archives LIMIT 1");
    echo "✅ Exists.\n";
} catch (Exception $e) {
    echo "❌ Missing.\n";
}
