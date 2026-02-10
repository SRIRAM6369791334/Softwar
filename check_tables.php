<?php
$c = require __DIR__ . '/config/database.php';
$pdo = new PDO("mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}", $c['username'], $c['password']);

echo "=== All Tables ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) echo "  $t\n";
echo "\nTotal: " . count($tables) . " tables\n\n";

// Check employee_attendance
echo "=== employee_attendance ===\n";
try {
    $cols = $pdo->query("DESCRIBE employee_attendance")->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns: " . implode(', ', $cols) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Check stock_transfers
echo "\n=== stock_transfers ===\n";
try {
    $cols = $pdo->query("DESCRIBE stock_transfers")->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns: " . implode(', ', $cols) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Check employee_roster
echo "\n=== employee_roster ===\n";
try {
    $cols = $pdo->query("DESCRIBE employee_roster")->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns: " . implode(', ', $cols) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Check employee_leaves
echo "\n=== employee_leaves ===\n";
try {
    $cols = $pdo->query("DESCRIBE employee_leaves")->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns: " . implode(', ', $cols) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Check overtime_requests
echo "\n=== overtime_requests ===\n";
try {
    $cols = $pdo->query("DESCRIBE overtime_requests")->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns: " . implode(', ', $cols) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
