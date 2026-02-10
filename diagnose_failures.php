<?php
/**
 * Diagnose remaining failing tests
 */
$c = require __DIR__ . '/config/database.php';
$pdo = new PDO("mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}", $c['username'], $c['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== employee_leaves ===\n";
try {
    $cols = $pdo->query("SHOW COLUMNS FROM employee_leaves")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "  {$col['Field']} ({$col['Type']}) {$col['Null']} {$col['Default']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== stock_transfers ===\n";
try {
    $cols = $pdo->query("SHOW COLUMNS FROM stock_transfers")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "  {$col['Field']} ({$col['Type']}) {$col['Null']} {$col['Default']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== branches ===\n";
try {
    $rows = $pdo->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) echo "  ID={$r['id']} Name={$r['name']}\n";
    if (empty($rows)) echo "  (empty - no branches)\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test: employee_leaves insert ===\n";
try {
    $pdo->exec("INSERT INTO employee_leaves (user_id, start_date, end_date, reason, status) VALUES (1, '2026-02-15', '2026-02-16', 'Test', 'pending')");
    $id = $pdo->lastInsertId();
    echo "OK: Inserted leave ID=$id\n";
    // Test update with processed_by
    $pdo->exec("UPDATE employee_leaves SET status = 'approved', processed_by = 1 WHERE id = $id");
    echo "OK: Updated leave to approved\n";
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

echo "\n=== Test: stock_transfers insert ===\n";
try {
    $targetBranch = $pdo->query("SELECT id FROM branches WHERE id != 1 LIMIT 1")->fetchColumn();
    echo "Target branch: " . ($targetBranch ?: '(none)') . "\n";
    if ($targetBranch) {
        $pdo->exec("INSERT INTO stock_transfers (from_branch, to_branch, status, created_by) VALUES (1, $targetBranch, 'pending', 1)");
        echo "OK: Transfer created\n";
    } else {
        echo "SKIP: No branch other than 1\n";
        // Create a second branch
        $pdo->exec("INSERT IGNORE INTO branches (id, name, code, address) VALUES (2, 'Branch 2', 'BR2', 'Test Address')");
        echo "Created branch 2 for testing\n";
    }
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

echo "\n=== SchedulerController check ===\n";
$file = __DIR__ . '/app/Controllers/SchedulerController.php';
echo file_exists($file) ? "EXISTS\n" : "MISSING\n";
