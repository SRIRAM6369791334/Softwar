<?php
/**
 * Supermarket OS - Master Backtest Runner
 */

$tests = [
    'Biometric Auth' => 'verify_biometric.php',
    'Vendor Isolation' => 'verify_vendor_isolation.php',
    'Map/Location' => 'verify_map.php',
    'POS Flow' => 'verify_pos.php',
    'Inventory/Stock' => 'verify_inventory.php',
    'Search Logic' => 'test_search.php',
    'Employee/HR' => 'verify_employees.php',
    'Admin/Groups' => 'verify_admin_mgmt.php',
    'Stock/Transfers' => 'verify_stock_flow.php',
    'Analytics/GST' => 'verify_analytics.php',
    'Maintenance/Log' => 'verify_maintenance.php'
];

echo "=========================================\n";
echo "       SUPERMARKET OS BACKTEST         \n";
echo "=========================================\n\n";

$passed = 0;
$failed = 0;
$results = [];

foreach ($tests as $name => $file) {
    echo "Running $name [$file]...\n";
    $output = [];
    $exitCode = 0;
    exec("php $file", $output, $exitCode);
    
    if ($exitCode === 0) {
        echo "✅ PASSED\n";
        $passed++;
        $results[$name] = 'PASS';
    } else {
        echo "❌ FAILED\n";
        $failed++;
        $results[$name] = 'FAIL';
        echo "   Error: " . end($output) . "\n";
    }
    echo "-----------------------------------------\n";
}

echo "\nBACKTEST SUMMARY\n";
echo "-----------------------------------------\n";
foreach ($results as $name => $status) {
    echo str_pad($name, 25) . ": " . ($status == 'PASS' ? "✅" : "❌") . " $status\n";
}
echo "-----------------------------------------\n";
echo "TOTAL: " . count($tests) . " | PASSED: $passed | FAILED: $failed\n";
echo "=========================================\n";

if ($failed > 0) exit(1);
exit(0);
