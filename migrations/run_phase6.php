<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║  PHASE 6: ADVANCED CONCURRENCY MIGRATION               ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

$sql = file_get_contents(__DIR__ . '/phase6_concurrency.sql');
$statements = explode(';', $sql);

foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (empty($stmt)) continue;
    
    try {
        $pdo->exec($stmt);
        echo "✅ Success: " . substr($stmt, 0, 50) . "...\n";
    } catch (\PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column') || str_contains($e->getMessage(), 'already exists')) {
            echo "⚠️  Already exists: " . substr($stmt, 0, 50) . "...\n";
        } else {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }
}
