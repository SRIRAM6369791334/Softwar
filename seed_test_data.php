<?php
/**
 * Supermarket OS - Test Data Seeder
 * Populates critical tables with data for testing and demonstration.
 */

define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/app/Core/Database.php';

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "--- Supermarket OS Seeder ---\n";

    // 1. Clear Existing Data (Optional - use with caution)
    // $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    // $pdo->exec("TRUNCATE branches; TRUNCATE vendors; ... ");
    // $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 2. Seed Branches (If not exists)
    echo "Seeding Branches...\n";
    $branches = [
        ['Main Branch (HQ)', 'Mumbai Central', 'North', 1],
        ['North Side Store', 'Andheri West', 'North', 1],
        ['South Side Mall', 'Colaba Causeway', 'South', 1]
    ];
    foreach ($branches as $b) {
        $pdo->prepare("INSERT IGNORE INTO branches (name, location, region, is_active) VALUES (?, ?, ?, ?)")
            ->execute($b);
    }
    $branchIds = $pdo->query("SELECT id FROM branches")->fetchAll(PDO::FETCH_COLUMN);

    // 3. Seed Vendors
    echo "Seeding Vendors...\n";
    $vendors = [
        ['Global Supplies Inc', 'contact@globalsupply.com', password_hash('vendor123', PASSWORD_BCRYPT), '9876543210'],
        ['Local Fresh produce', 'local@produce.com', password_hash('vendor123', PASSWORD_BCRYPT), '9123456789'],
        ['Tech Logistics', 'info@techlogistics.com', password_hash('vendor123', PASSWORD_BCRYPT), '7788990011']
    ];
    foreach ($vendors as $v) {
        $pdo->prepare("INSERT IGNORE INTO vendors (name, email, password_hash, phone) VALUES (?, ?, ?, ?)")
            ->execute($v);
    }
    $vendorIds = $pdo->query("SELECT id FROM vendors")->fetchAll(PDO::FETCH_COLUMN);

    // 4. Seed Products
    echo "Seeding Products...\n";
    $products = [
        [2, 'Organic Whole Milk 1L', 'MLK001', '0401', 'Nos', 20], // 2 = GST 5%
        [1, 'Premium Sliced Bread', 'BRD001', '1905', 'Nos', 15],  // 1 = Exempt
        [1, 'Farm Fresh Eggs (Doz)', 'EGG001', '0407', 'Nos', 10],
        [1, 'Red Delicious Apples 1kg', 'APL001', '0808', 'Kg', 30],
        [2, 'Basmati Extra Long Rice 5kg', 'RCE001', '1006', 'Nos', 5],
        [4, 'Energy Drink 250ml', 'EDK001', '2202', 'Nos', 50]     // 4 = GST 18%
    ];
    foreach ($products as $p) {
        $pdo->prepare("INSERT IGNORE INTO products (tax_group_id, name, sku, hsn_code, unit, min_stock_alert) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute($p);
    }
    $productIds = $pdo->query("SELECT id FROM products")->fetchAll(PDO::FETCH_COLUMN);

    // 5. Seed Product Batches (One per branch per product)
    echo "Seeding Batches & Stock...\n";
    foreach ($branchIds as $bid) {
        foreach ($productIds as $pid) {
            // High stock batch
            $pdo->prepare("INSERT INTO product_batches (product_id, batch_no, expiry_date, purchase_price, mrp, sale_price, stock_qty) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)")
                ->execute([$pid, 'BCH-'.rand(100,999), date('Y-m-d', strtotime('+6 months')), 50, 100, 95, rand(50, 100)]);
            
            // Low stock batch (to trigger alerts)
            $pdo->prepare("INSERT INTO product_batches (product_id, batch_no, expiry_date, purchase_price, mrp, sale_price, stock_qty) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)")
                ->execute([$pid, 'LSB-'.rand(100,999), date('Y-m-d', strtotime('+3 months')), 50, 100, 95, 2]);

            // Near Expiry batch (to trigger alerts)
            $pdo->prepare("INSERT INTO product_batches (product_id, batch_no, expiry_date, purchase_price, mrp, sale_price, stock_qty) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)")
                ->execute([$pid, 'EXP-'.rand(100,999), date('Y-m-d', strtotime('+7 days')), 50, 100, 95, 10]);
        }
    }

    // 6. Seed Invoices (Simulate past 7 days sales)
    echo "Seeding Sales Data (Invoices)...\n";
    $userId = 1; // Assuming Admin exists
    for ($i = 0; $i < 20; $i++) {
        $daysAgo = rand(0, 7);
        $date = date('Y-m-d H:i:s', strtotime("-$daysAgo days"));
        $invNo = 'INV-' . time() . '-' . rand(100, 999);
        
        $pdo->prepare("INSERT INTO invoices (user_id, invoice_no, sub_total, tax_total, grand_total, payment_mode, created_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([$userId, $invNo, 400, 72, 472, 'cash', $date]);
        
        $invId = $pdo->lastInsertId();
        
        // Add one item per invoice
        $pdo->prepare("INSERT INTO invoice_items (invoice_id, product_id, batch_id, qty, unit_price, tax_percent, tax_amount, total) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$invId, 1, 1, 4, 100, 18, 72, 472]);
    }

    // 7. Seed Purchase Orders
    echo "Seeding Purchase Orders...\n";
    foreach ($branchIds as $bid) {
        $pdo->prepare("INSERT INTO purchase_orders (vendor_id, branch_id, order_no, total_amount, status, created_by) 
                       VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$vendorIds[0], $bid, 'PO-'.rand(1000,9999), 15000, 'delivered', $userId]);
            
        $pdo->prepare("INSERT INTO purchase_orders (vendor_id, branch_id, order_no, total_amount, status, created_by) 
                       VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$vendorIds[1], $bid, 'PO-'.rand(1000,9999), 5000, 'ordered', $userId]);
    }

    // 8. Seed Map Sections (Fix for Map test)
    echo "Seeding Map Sections...\n";
    foreach ($branchIds as $bid) {
        $pdo->prepare("INSERT INTO map_sections (branch_id, name, grid_width, grid_height) VALUES (?, ?, ?, ?)")
            ->execute([$bid, 'Main Floor', 10, 10]);
    }

    echo "\nSeeding Completed Successfully!\n";

} catch (Exception $e) {
    echo "\nCRITICAL ERROR: " . $e->getMessage() . "\n";
}
