<?php

/**
 * Supermarket OS - Entry Point
 * "No Framework" Architecture
 */

declare(strict_types=1);

// 1. Define Root Path
define('APP_ROOT', dirname(__DIR__));

// Handle Static Files for PHP Built-in Server
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $url;
    if (is_file($file)) {
        return false;
    }
}

// 2. Autoloader (Simple PSR-4 Implementation)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_ROOT . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 3. Error Handling
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', APP_ROOT . '/logs/' . date('Y-m-d') . '.log');

// 4. Boostrap App
use App\Core\Application;

try {
    $app = new Application();
    
    // Load Routes
    require APP_ROOT . '/app/routes.php';

    $app->run();
} catch (Throwable $e) {
    // Basic Fallback for Critical Failures
    http_response_code(500);
    error_log("[CRITICAL] " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    echo "<h1>System Error</h1><p>Please contact support. Ref: " . date('Ymd-His') . "</p>";
}
