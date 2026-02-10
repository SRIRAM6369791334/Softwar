<?php
/**
 * Database Migration Runner
 * Applies security and performance migration
 */

require_once __DIR__ . '/../app/bootstrap.php';

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║  SECURITY & PERFORMANCE MIGRATION                      ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

// Read migration file
$sqlFile = __DIR__ . '/security_and_performance.sql';
if (!file_exists($sqlFile)) {
    die("ERROR: Migration file not found: $sqlFile\n");
}

$sql = file_get_contents($sqlFile);

// Split into individual statements
$statements = [];
$currentStatement = '';
$inComment = false;

foreach (explode("\n", $sql) as $line) {
    $trimmed = trim($line);
    
    // Skip empty lines
    if ($trimmed === '') continue;
    
    // Skip single-line comments
    if (str_starts_with($trimmed, '--')) continue;
    
    // Handle multi-line comments
    if (str_starts_with($trimmed, '/*')) {
        $inComment = true;
    }
    if ($inComment) {
        if (str_contains($trimmed, '*/')) {
            $inComment = false;
        }
        continue;
    }
    
    $currentStatement .= $line . "\n";
    
    // Statement complete
    if (str_ends_with($trimmed, ';')) {
        $stmt = trim($currentStatement);
        if (!empty($stmt)) {
            $statements[] = $stmt;
        }
        $currentStatement = '';
    }
}

echo "Found " . count($statements) . " SQL statements to execute\n\n";

$success = 0;
$errors = [];
$warnings = [];

foreach ($statements as $i => $statement) {
    $preview = substr(str_replace(["\n", "\r"], ' ', $statement), 0, 60);
    echo "Executing [" . ($i + 1) . "/" . count($statements) . "]: $preview...\n";
    
    try {
        $pdo->exec($statement);
        $success++;
        echo "  ✅ Success\n";
    } catch (\PDOException $e) {
        $errorMsg = $e->getMessage();
        
        // These are acceptable "errors" - already exists
        if (str_contains($errorMsg, 'Duplicate key name') ||
            str_contains($errorMsg, 'Duplicate column name') ||
            str_contains($errorMsg, 'already exists')) {
            $warnings[] = "$preview - Already exists";
            echo "  ⚠️  Already exists (skipped)\n";
        } else {
            $errors[] = "$preview - " . $errorMsg;
            echo "  ❌ Error: " . $errorMsg . "\n";
        }
    }
}

echo "\n╔════════════════════════════════════════════════════════╗\n";
echo "║  MIGRATION RESULTS                                     ║\n";
echo "╠════════════════════════════════════════════════════════╣\n";
echo "║  Total Statements: " . str_pad(count($statements), 30) . "║\n";
echo "║  ✅ Successful: " . str_pad($success, 33) . "║\n";
echo "║  ⚠️  Warnings: " . str_pad(count($warnings), 35) . "║\n";
echo "║  ❌ Errors: " . str_pad(count($errors), 37) . "║\n";
echo "╚════════════════════════════════════════════════════════╝\n";

if (!empty($errors)) {
    echo "\nErrors encountered:\n";
    foreach (array_slice($errors, 0, 10) as $error) {
        echo "  - $error\n";
    }
}

if (count($errors) === 0) {
    echo "\n🎉 Migration completed successfully!\n";
    exit(0);
} else {
    echo "\n⚠️  Migration completed with errors. Review the output above.\n";
    exit(1);
}
