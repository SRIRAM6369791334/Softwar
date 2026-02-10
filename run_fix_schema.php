<?php
require 'app/bootstrap.php';

$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

$sql = file_get_contents('fix_products_schema.sql');

try {
    // Execute the SQL. Note: PDO::exec doesn't support multiple statements by default in some configs,
    // but many MySQL drivers do if enabled. Alternatively, split by semicolon.
    // However, the cleanest way is often just to run it.
    $pdo->exec($sql);
    echo "Database schema updated successfully.\n";
} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
    exit(1);
}
