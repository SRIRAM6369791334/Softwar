<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting Phase 23 (Part 2): Updating Users Table...\n";

try {
    // Check if columns exist to avoid duplicate column errors if run multiple times
    $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('failed_login_attempts', $columns)) {
        echo "Adding 'failed_login_attempts' column...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN failed_login_attempts INT DEFAULT 0");
    }

    if (!in_array('locked_until', $columns)) {
        echo "Adding 'locked_until' column...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN locked_until TIMESTAMP NULL DEFAULT NULL");
    }

    if (!in_array('last_password_change', $columns)) {
        echo "Adding 'last_password_change' column...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN last_password_change TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }

    echo "Users table updated successfully.\n";

} catch (PDOException $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Phase 23 (Part 2) Migration Complete!\n";
