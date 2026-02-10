<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting Phase 23/25 Migration: User Contact Info...\n";

try {
    $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('email', $columns)) {
        echo "Adding 'email' column...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE AFTER full_name");
    }

    if (!in_array('phone', $columns)) {
        echo "Adding 'phone' column...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email");
    }

    echo "User contact info columns added.\n";

} catch (PDOException $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Phase 23/25 Migration Complete!\n";
