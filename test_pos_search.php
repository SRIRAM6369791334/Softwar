<?php
require 'app/bootstrap.php';
$_SESSION['branch_id'] = 1; // Simulate branch session

$db = \App\Core\Database::getInstance();
$checkoutService = new \App\Services\CheckoutService($db);
$pos = new \App\Controllers\PosController($db, $checkoutService);

echo "--- POS SEARCH TEST (Query: 'Product 1') ---\n";
$_GET['q'] = 'Product 1';

// We need to capture the JSON output. 
// Since PosController::json() calls echo and exit, we might need a workaround or just check the logic.
// In this test environment, we'll just check the query results manually.

$query = 'Product 1';
$branchId = 1;
$sql = "
    SELECT p.id as product_id, p.name as product_name, 
           pv.id as variant_id, pv.variant_name, pv.barcode, pv.sku_code as sku,
           pv.selling_price as sale_price, pv.current_stock as stock_qty,
           tg.percentage as tax_percent
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.id
    LEFT JOIN tax_groups tg ON pv.tax_slab_id = tg.id
    WHERE (p.name LIKE ? OR pv.barcode = ? OR pv.sku_code = ?) 
    AND pv.current_stock > 0
    AND p.branch_id = ?
    AND p.is_active = 1
    AND pv.is_active = 1
    AND p.deleted_at IS NULL
    ORDER BY p.name ASC
    LIMIT 20
";

$results = $db->query($sql, ["%$query%", $query, $query, $branchId])->fetchAll();
echo "Found " . count($results) . " results.\n";
foreach ($results as $r) {
    echo "- {$r['product_name']} [{$r['variant_name']}] (SKU: {$r['sku']}, Price: {$r['sale_price']}, Stock: {$r['stock_qty']})\n";
}
