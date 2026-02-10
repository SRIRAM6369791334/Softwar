<?php
$c = require __DIR__ . '/config/database.php';
$pdo = new PDO("mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}", $c['username'], $c['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== stock_transfers columns ===\n";
$cols = $pdo->query("SHOW COLUMNS FROM stock_transfers")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $r) {
    echo "  {$r['Field']} | {$r['Type']} | Null:{$r['Null']} | Default:" . ($r['Default'] ?? '(none)') . "\n";
}

echo "\n=== Test insert with from_branch_id, to_branch_id ===\n";
try {
    $pdo->exec("INSERT INTO stock_transfers (from_branch_id, to_branch_id, status, created_by) VALUES (1, 2, 'pending', 1)");
    echo "OK: Direct insert worked\n";
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

echo "\n=== Check transfer_no column ===\n";
try {
    $pdo->exec("INSERT INTO stock_transfers (from_branch_id, to_branch_id, transfer_no, status, created_by) VALUES (1, 2, 'TRN-TEST-" . time() . "', 'pending', 1)");
    echo "OK: Insert with transfer_no worked\n";
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
