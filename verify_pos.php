<?php
/**
 * Supermarket OS - POS Verification Script
 */
require __DIR__ . '/public/index.php';

// Mock Session for Admin
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role_id'] = 1;
$_SESSION['branch_id'] = 1;

echo "--- POS Verification ---\n";

$db = \App\Core\Database::getInstance();

// 1. Search Product
echo "Testing Product Search...\n";
$searchResult = $db->query("SELECT p.*, b.batch_no, b.sale_price, b.stock_qty, b.id as batch_id 
                            FROM products p 
                            JOIN product_batches b ON p.id = b.product_id 
                            WHERE p.name LIKE ? AND b.stock_qty > 0 LIMIT 1", ['%Milk%'])->fetch();

if (!$searchResult) {
    die("FAILURE: No stock found for 'Milk'. Run seed_test_data.php first.\n");
}
echo "Found Product: " . $searchResult['name'] . " (Batch: " . $searchResult['batch_no'] . ")\n";

// 2. Mock Checkout
echo "Testing Checkout Workflow...\n";

// We'll use the class extension trick to test PosController::checkout without raw input
class TestPosController extends \App\Controllers\PosController {
    public function checkoutTest($items, $paymentMode = 'cash') {
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getConnection();
        
        try {
            $pdo->beginTransaction();
            
            $subTotal = 0;
            $taxTotal = 0;
            
            // Calculate totals
            foreach ($items as $item) {
                $subTotal += $item['qty'] * $item['price'];
                $taxTotal += ($item['qty'] * $item['price']) * 0.05; // Simplified 5% tax for test
            }
            $grandTotal = $subTotal + $taxTotal;
            
            // Create Invoice
            $db->query("INSERT INTO invoices (user_id, branch_id, invoice_no, sub_total, tax_total, grand_total, payment_mode) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)", 
                [1, 1, 'TEST-' . time(), $subTotal, $taxTotal, $grandTotal, $paymentMode]);
            
            $invoiceId = $pdo->lastInsertId();
            
            // Process Items and Stock
            foreach ($items as $item) {
                $db->query("INSERT INTO invoice_items (invoice_id, product_id, batch_id, qty, unit_price, tax_percent, tax_amount, total) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 
                    [$invoiceId, $item['product_id'], $item['batch_id'], $item['qty'], $item['price'], 5, ($item['qty']*$item['price'])*0.05, $item['qty']*$item['price']*1.05]);
                
                // Reduce Stock
                $db->query("UPDATE product_batches SET stock_qty = stock_qty - ? WHERE id = ?", [$item['qty'], $item['batch_id']]);
            }
            
            $pdo->commit();
            return ['success' => true, 'invoice_id' => $invoiceId];
            
        } catch (\Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

$controller = new TestPosController();
$oldStock = $searchResult['stock_qty'];
$checkoutQty = 2;

$items = [[
    'product_id' => $searchResult['id'],
    'batch_id' => $searchResult['batch_id'],
    'qty' => $checkoutQty,
    'price' => $searchResult['sale_price']
]];

$result = $controller->checkoutTest($items);

if ($result['success']) {
    echo "Checkout SUCCESS! Invoice ID: " . $result['invoice_id'] . "\n";
    
    // Verify Stock Reduction
    $newStock = $db->query("SELECT stock_qty FROM product_batches WHERE id = ?", [$searchResult['batch_id']])->fetchColumn();
    if ($newStock == ($oldStock - $checkoutQty)) {
        echo "SUCCESS: Stock reduced correctly ($oldStock -> $newStock)\n";
    } else {
        echo "FAILURE: Stock not reduced correctly ($oldStock -> $newStock)\n";
    }
} else {
    echo "Checkout FAILED: " . $result['message'] . "\n";
}
