<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting Phase 24 Migration: Data Tools...\n";

try {
    // 1. Add deleted_at columns for Soft Deletes
    $tables = ['products', 'users', 'invoices'];
    
    foreach ($tables as $table) {
        // Check if table exists first
        $tableExists = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount() > 0;
        if ($tableExists) {
            $columns = $pdo->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('deleted_at', $columns)) {
                echo "Adding 'deleted_at' to '$table'...\n";
                $pdo->exec("ALTER TABLE $table ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
            }
        }
    }

    // 2. Create Archives Table
    echo "Creating 'archives' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS archives (
        id INT AUTO_INCREMENT PRIMARY KEY,
        original_table VARCHAR(50) NOT NULL,
        original_id INT NOT NULL,
        data JSON NOT NULL,
        archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        archived_by INT,
        reason VARCHAR(255),
        INDEX (original_table),
        INDEX (archived_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
    echo "Table 'archives' created.\n";

} catch (PDOException $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Phase 24 Migration Complete!\n";
