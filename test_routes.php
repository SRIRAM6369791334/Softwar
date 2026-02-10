<?php
/**
 * Automated Route Tester
 * Tests all application routes for 500 errors after Database fix
 */

echo "🧪 Testing Application Routes\n";
echo "=============================\n\n";

// High-priority routes that make heavy DB queries
$priorityRoutes = [
    '/dashboard',
    '/reports/daybook',
    '/pos',
    '/inventory',
    '/products',
    '/branches',
    '/admin/employee/roster',
    '/admin/employee/leaves',
    '/admin/employee/timesheets',
    '/admin/employee/messages',
    '/admin/employee/overtime',
    '/admin/taxes',
    '/users',
];

// Additional routes to test
$additionalRoutes = [
    '/map',
    '/reports/top-products',
    '/reports/employee-performance',
    '/reports/reorder',
    '/reports/gst',
    '/inventory/inward',
    '/inventory/transfers',
    '/inventory/po',
    '/central/dashboard',
    '/admin/vendors',
    '/admin/logs',
    '/admin/logs/dashboard',
];

$allRoutes = array_merge($priorityRoutes, $additionalRoutes);

$results = [
    'passed' => [],
    'failed' => [],
    'redirect' => [],
    'other' => [],
];

echo "Testing " . count($allRoutes) . " routes...\n\n";

foreach ($allRoutes as $route) {
    $ch = curl_init("http://localhost:8000$route");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Color codes for output
    $status = '';
    if ($httpCode === 500) {
        $status = "❌ FAILED";
        $results['failed'][] = $route;
    } else if ($httpCode === 200) {
        $status = "✅ PASSED";
        $results['passed'][] = $route;
    } else if ($httpCode === 302 || $httpCode === 301) {
        $status = "🔄 REDIRECT";
        $results['redirect'][] = $route;
    } else {
        $status = "⚠️  OTHER";
        $results['other'][] = "$route (HTTP $httpCode)";
    }
    
    echo sprintf("%-50s %s (HTTP %d)\n", $route, $status, $httpCode);
    
    // Small delay to avoid overwhelming the server
    usleep(100000); // 100ms
}

echo "\n=============================\n";
echo "📊 Test Summary\n";
echo "=============================\n";
echo "✅ Passed: " . count($results['passed']) . "\n";
echo "🔄 Redirects: " . count($results['redirect']) . "\n";
echo "❌ Failed (500 errors): " . count($results['failed']) . "\n";
echo "⚠️  Other statuses: " . count($results['other']) . "\n";

if (!empty($results['failed'])) {
    echo "\n🚨 FAILED ROUTES (Need Investigation):\n";
    foreach ($results['failed'] as $route) {
        echo "   - $route\n";
    }
} else {
    echo "\n🎉 All routes tested successfully! No 500 errors found.\n";
}

if (!empty($results['other'])) {
    echo "\n⚠️  Routes with other status codes:\n";
    foreach ($results['other'] as $routeInfo) {
        echo "   - $routeInfo\n";
    }
}

echo "\n";
