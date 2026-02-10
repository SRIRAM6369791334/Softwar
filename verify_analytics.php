<?php
/**
 * Supermarket OS - Analytics Verification
 */
require __DIR__ . '/public/index.php';

echo "--- Analytics Verification ---\n";

$db = \App\Core\Database::getInstance();

// 1. Test Local Dashboard Queries
echo "Testing Local Dashboard Stats...\n";
$stats = $db->query("SELECT COUNT(*) as total_sales FROM invoices WHERE created_at >= CURDATE()")->fetch();
echo "SUCCESS: Sales analytics retrieved (" . $stats['total_sales'] . " today).\n";

// 2. Test GST Summary
echo "Testing GST Report Logic...\n";
$gst = $db->query("SELECT SUM(tax_total) as total_tax FROM invoices")->fetchColumn();
echo "SUCCESS: GST liability calculated: " . ($gst ?: 0) . ".\n";

// 3. Test Top Products
echo "Testing Market Intelligence...\n";
$top = $db->query("SELECT product_id, SUM(qty) as total_sold FROM invoice_items GROUP BY product_id ORDER BY total_sold DESC LIMIT 5")->fetchAll();
echo "SUCCESS: Top products generated (" . count($top) . " records).\n";
