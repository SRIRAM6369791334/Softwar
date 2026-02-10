<?php
/**
 * Health Check Endpoint
 * Provides system status information
 */

header('Content-Type: application/json');

$status = 'healthy';
$checks = [];

try {
    // Database check
    require_once __DIR__ . '/../app/bootstrap.php';
    $db = \App\Core\Database::getInstance();
    $db->query("SELECT 1")->fetch();
    $checks['database'] = ['status' => 'ok'];
} catch (\Exception $e) {
    $status = 'unhealthy';
    $checks['database'] = ['status' => 'error', 'message' => $e->getMessage()];
}

// Disk space check
$diskFree = disk_free_space('/');
$diskTotal = disk_total_space('/');
$diskPercent = ($diskFree / $diskTotal) * 100;
$checks['disk_space'] = [
    'status' => $diskPercent > 10 ? 'ok' : 'warning',
    'free_percent' => round($diskPercent, 2)
];

// Session directory check
$sessionPath = session_save_path() ?: sys_get_temp_dir();
$checks['session_storage'] = [
    'status' => is_writable($sessionPath) ? 'ok' : 'error',
    'path' => $sessionPath
];

// Response
$response = [
    'status' => $status,
    'timestamp' => date('c'),
    'checks' => $checks
];

http_response_code($status === 'healthy' ? 200 : 503);
echo json_encode($response, JSON_PRETTY_PRINT);
