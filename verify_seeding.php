<?php
require 'app/bootstrap.php';

$db = \App\Core\Database::getInstance();

echo "--- DATA VERIFICATION ---\n";

$tables = [
    'categories', 'subcategories', 'brands', 'subbrands', 
    'units', 'storage_types', 'tax_groups', 'suppliers', 
    'products', 'product_variants'
];

foreach ($tables as $table) {
    try {
        $count = $db->query("SELECT COUNT(*) as total FROM `$table`")->fetch()['total'];
        echo "Table $table: $count rows\n";
    } catch (Exception $e) {
        echo "Table $table: ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\n--- SAMPLE PRODUCT CHECK ---\n";
// Check product 1 and its variants
try {
    $product = $db->query("SELECT p.name, c.name as category, b.name as brand 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN brands b ON p.brand_id = b.id 
                          WHERE p.id = 1")->fetch();
    
    if ($product) {
        echo "Product ID 1: {$product['name']} (Category: {$product['category']}, Brand: {$product['brand']})\n";
        
        $variants = $db->query("SELECT variant_name, sku_code, selling_price 
                               FROM product_variants 
                               WHERE product_id = 1")->fetchAll();
        
        echo "Variants for Product 1:\n";
        foreach ($variants as $v) {
            echo "  - {$v['variant_name']} (SKU: {$v['sku_code']}, Price: {$v['selling_price']})\n";
        }
    } else {
        echo "Product ID 1 not found.\n";
    }
} catch (Exception $e) {
    echo "Error during sample check: " . $e->getMessage() . "\n";
}
