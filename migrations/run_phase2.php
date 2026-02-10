<?php
/**
 * Phase 2 Migration Runner
 */

require_once __DIR__ . '/../app/bootstrap.php';

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║  PHASE 2: AUTHENTICATION & AUTHORIZATION MIGRATION     ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

$sqlFile = __DIR__ . '/phase2_authentication.sql';
$sql = file_get_contents($sqlFile);

// Parse SQL statements
$statements = [];
$currentStatement = '';
foreach (explode("\n", $sql) as $line) {
    $trimmed = trim($line);
    if ($trimmed === '' || str_starts_with($trimmed, '--')) continue;
    
    $currentStatement .= $line . "\n";
    if (str_ends_with($trimmed, ';')) {
        $statements[] = trim($currentStatement);
        $currentStatement = '';
    }
}

echo "Found " . count($statements) . " SQL statements\n\n";

$success = 0;
$errors = 0;

foreach ($statements as $i => $stmt) {
    $preview = substr(str_replace(["\n", "\r"], ' ', $stmt), 0, 70);
    echo "[" . ($i + 1) . "/" . count($statements) . "] $preview...\n";
    
    try {
        $pdo->exec($stmt);
        echo "  ✅ Success\n";
        $success++;
    } catch (\PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column')) {
            echo "  ⚠️  Column already exists\n";
        } else {
            echo "  ❌ Error: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
}

echo "\n╔════════════════════════════════════════════════════════╗\n";
echo "║  RESULTS: $success successful, $errors errors          \n";
echo "╚════════════════════════════════════════════════════════╝\n";

exit($errors > 0 ? 1 : 0);
