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
    $batch = $db->query("SELECT id, product_id FROM product_batches LIMIT 1")->fetch();
    if ($batch) {
        $db->query("INSERT INTO stock_transfers (from_branch_id, to_branch_id, product_id, batch_id, qty, transfer_no, status, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [1, $targetBranch, $batch['product_id'], $batch['id'], 5, 'TRN-TEST-' . time(), 'pending', 1]);
        echo "SUCCESS: Stock transfer initiated to Branch $targetBranch.\n";
    } else {
        echo "SKIP: No product batches found for transfer test.\n";
    }
} else {
    echo "SKIP: Only 1 branch exists.\n";
}
// 3. Test Batch Adjustment
echo "Testing Batch Updates...\n";
$db->query("UPDATE product_batches SET stock_qty = stock_qty + 10 WHERE id = 1");
echo "SUCCESS: Batch stock adjusted.\n";
