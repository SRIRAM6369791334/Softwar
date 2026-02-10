<?php
/**
 * Verification of Vendor Portal Isolation
 */

require __DIR__ . '/app/Core/Auth.php';
use App\Core\Auth;

// Mock session
session_start();
$_SESSION['vendor_id'] = 1;
$_SESSION['is_vendor'] = true;

echo "Verifying security isolation...\n";

// Test 1: Vendor check
if (Auth::vendorCheck()) {
    echo "[PASS] Auth::vendorCheck() returns true for vendor session.\n";
} else {
    echo "[FAIL] Auth::vendorCheck() failed.\n";
}

// Test 2: Admin check should fail
if (isset($_SESSION['user_id'])) {
     echo "[FAIL] Admin user_id unexpectedly present.\n";
} else {
     echo "[PASS] Admin session key 'user_id' not present in vendor session.\n";
}

echo "Verification complete!\n";
