<?php
if ($argc < 2) {
    die("Usage: php run_migration.php <sql_file>\n");
}

require 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

$sqlFile = $argv[1];
if (!file_exists($sqlFile)) {
    die("Error: File $sqlFile not found.\n");
}

$sql = file_get_contents($sqlFile);

try {
    $pdo->exec($sql);
    echo "Migration successful: $sqlFile applied.\n";
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
