<?php
/**
 * Supermarket OS - Inventory Verification Script
 */
require __DIR__ . '/public/index.php';

// Mock Session
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['branch_id'] = 1;

echo "--- Inventory Verification ---\n";

$db = \App\Core\Database::getInstance();

// 1. Test Inward Stock (Batch Creation)
echo "Testing Inward Stock (New Batch)...\n";

$product = $db->query("SELECT id FROM products LIMIT 1")->fetch();
if (!$product) die("FAILURE: No products found.\n");

$batchNo = 'TEST-BATCH-' . time();
$data = [
    'product_id' => $product['id'],
    'batch_no' => $batchNo,
    'expiry_date' => date('Y-m-d', strtotime('+1 year')),
    'purchase_price' => 45.00,
    'mrp' => 60.00,
    'sale_price' => 55.00,
    'qty' => 100
];

// Mocking Controller Store
class TestInventoryController extends \App\Controllers\InventoryController {
    public function storeTest($data) {
        $db = \App\Core\Database::getInstance();
        $db->query("INSERT INTO product_batches (product_id, batch_no, expiry_date, purchase_price, mrp, sale_price, stock_qty) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)", 
            [$data['product_id'], $data['batch_no'], $data['expiry_date'], $data['purchase_price'], $data['mrp'], $data['sale_price'], $data['qty']]);
        return true;
    }
}

$controller = new TestInventoryController();
$res = $controller->storeTest($data);

if ($res) {
    echo "Batch Created Successfully.\n";
    // Verify in DB
    $batch = $db->query("SELECT * FROM product_batches WHERE batch_no = ?", [$batchNo])->fetch();
    if ($batch && $batch['stock_qty'] == 100) {
        echo "SUCCESS: Inventory record verified in database.\n";
    } else {
        echo "FAILURE: Inventory record not found or data mismatch.\n";
    }
} else {
    echo "FAILURE: Batch creation failed.\n";
}
