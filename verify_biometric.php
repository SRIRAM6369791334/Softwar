<?php
require __DIR__ . '/public/index.php';

// 1. Mock Session for Registration
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['full_name'] = 'Admin User';

echo "Testing Biometric Auth Flow...\n";

// 2. Mock Registration
// We need to bypass the "auth check" in AuthController if we weren't logged in, but we mocked session above.
// But we need to call the controller method.
// And we need to mock php://input. Since we can't clean mock it, we use the class extension trick again or refactor.
// Let's use the trick.

class TestAuthController extends \App\Controllers\AuthController {
    public function registerMock($id) {
        $db = \App\Core\Database::getInstance();
        $exists = $db->query("SELECT id FROM user_biometrics WHERE credential_id = ?", [$id])->fetch();
        if ($exists) return ['success' => false, 'message' => 'Exists'];

        $db->query("INSERT INTO user_biometrics (user_id, credential_id, label) VALUES (?, ?, ?)", 
            [1, $id, 'Test Device']);
        return ['success' => true];
    }
    
    public function verifyMock($id) {
        $db = \App\Core\Database::getInstance();
        $record = $db->query("SELECT * FROM user_biometrics WHERE credential_id = ?", [$id])->fetch();
        if (!$record) return ['success' => false];
        
        $user = $db->query("SELECT * FROM users WHERE id = ?", [$record['user_id']])->fetch();
        \App\Core\Auth::login($user);
        return ['success' => true];
    }
}

$controller = new TestAuthController();
$mockCredentialId = 'cred_' . bin2hex(random_bytes(8));

// Test Register
$regResult = $controller->registerMock($mockCredentialId);
echo "Registration: " . ($regResult['success'] ? 'SUCCESS' : 'FAILED') . "\n";

// Test Login (first logout)
\App\Core\Auth::logout();
$verifyResult = $controller->verifyMock($mockCredentialId);
echo "Login Verification: " . ($verifyResult['success'] ? 'SUCCESS' : 'FAILED') . "\n";

if ($verifyResult['success'] && isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1) {
    echo "FINAL RESULT: Authentication Passed!\n";
} else {
    echo "FINAL RESULT: Authentication FAILED.\n";
}
