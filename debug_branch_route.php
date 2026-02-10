<?php
// Debug script to test branch routes
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/app.php';

// Simulate GET request to /branches/create
$_SERVER['REQUEST_URI'] = '/branches/create';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require __DIR__ . '/public/index.php';
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
}
