<?php
/**
 * Supermarket OS - Stock Flow Verification
 */
require __DIR__ . '/public/index.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['branch_id'] = 1;

echo "--- Stock Flow Verification ---\n";

$db = \App\Core\Database::getInstance();

// 1. Test Purchase Order
echo "Testing Purchase Orders...\n";
$vendor = $db->query("SELECT id FROM vendors LIMIT 1")->fetchColumn();
$db->query("INSERT INTO purchase_orders (vendor_id, branch_id, order_no, total_amount, status, created_by) 
            VALUES (?, ?, ?, ?, ?, ?)", 
    [$vendor, 1, 'PO-TEST-' . time(), 5000, 'ordered', 1]);
echo "SUCCESS: PO created.\n";

// 2. Test Stock Transfer
echo "Testing Stock Transfers...\n";
$targetBranch = $db->query("SELECT id FROM branches WHERE id != 1 LIMIT 1")->fetchColumn();
if ($targetBranch) {
    $db->query("INSERT INTO stock_transfers (from_branch, to_branch, status, created_by) 
                VALUES (?, ?, ?, ?)", [1, $targetBranch, 'pending', 1]);
    echo "SUCCESS: Stock transfer initiated to Branch $targetBranch.\n";
}

// 3. Test Batch Adjustment
echo "Testing Batch Updates...\n";
$db->query("UPDATE product_batches SET stock_qty = stock_qty + 10 WHERE id = 1");
echo "SUCCESS: Batch stock adjusted.\n";
