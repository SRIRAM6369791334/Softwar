<?php
/**
 * Supermarket OS - Admin Management Verification
 */
require __DIR__ . '/public/index.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role_id'] = 1;

echo "--- Admin Management Verification ---\n";

$db = \App\Core\Database::getInstance();

// 1. Test Branch Creation
echo "Testing Branch Management...\n";
$branchName = 'Test Branch ' . rand(100, 999);
$db->query("INSERT INTO branches (name, location, region, is_active) VALUES (?, ?, ?, ?)", 
    [$branchName, 'Test Location', 'Central', 1]);
$branchId = $db->getConnection()->lastInsertId();
if ($branchId) echo "SUCCESS: Branch '$branchName' created.\n";

// 2. Test User Creation
echo "Testing User Management...\n";
$username = 'tester_' . rand(100, 999);
$db->query("INSERT INTO users (role_id, username, password_hash, full_name, status) VALUES (?, ?, ?, ?, ?)", 
    [3, $username, password_hash('pass123', PASSWORD_BCRYPT), 'Test User', 'active']);
if ($db->query("SELECT id FROM users WHERE username = ?", [$username])->fetch()) {
    echo "SUCCESS: User '$username' created.\n";
}

// 3. Test Tax Creation
echo "Testing Tax Management...\n";
$taxName = 'Special GST ' . rand(1, 100);
$db->query("INSERT INTO tax_groups (name, percentage) VALUES (?, ?)", [$taxName, 7.50]);
if ($db->query("SELECT id FROM tax_groups WHERE name = ?", [$taxName])->fetch()) {
    echo "SUCCESS: Tax group '$taxName' created.\n";
}
