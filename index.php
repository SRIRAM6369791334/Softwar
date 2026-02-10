<?php
/**
 * Entry Point for PHP Built-in Server
 * Routes all requests through the application
 */

// If requesting a real file, serve it
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false; // Serve the file directly
    }
}

// Otherwise, route through the application
require_once __DIR__ . '/app/bootstrap.php';
