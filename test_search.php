<?php
require __DIR__ . '/public/index.php';

use App\Services\Search\SearchService;

echo "Testing Search Service...\n";

// 1. Get Engine
$engine = SearchService::getEngine();
echo "Engine Loaded: " . get_class($engine) . "\n";

// 2. Perform Search (Database Driver)
$query = "Milk";
echo "Searching for '$query'...\n";
$results = $engine->search($query, 'products');

echo "Found " . count($results) . " results.\n";

if (count($results) > 0) {
    foreach ($results as $res) {
        echo " - [{$res['id']}] {$res['name']} ({$res['sku']})\n";
    }
} else {
    echo "No results found. (Make sure you have products named '$query')\n";
}

// 3. Test Indexing (No-op for DB driver, but shouldn't crash)
$success = $engine->index('products', 999, ['name' => 'Test Item']);
echo "Index Op: " . ($success ? 'OK' : 'FAIL') . "\n";
