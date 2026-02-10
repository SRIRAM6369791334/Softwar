<?php
require 'app/bootstrap.php';

$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

$sql = file_get_contents('refine_schema.sql');

try {
    // Multi-query support depends on driver, let's run it
    $pdo->exec($sql);
    echo "Schema refined successfully.\n";
} catch (PDOException $e) {
    echo "Error refining schema: " . $e->getMessage() . "\n";
    exit(1);
}
