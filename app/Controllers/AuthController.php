<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;
use App\Core\Request;

class AuthController extends Controller
{
    public function login()
    {
        // If already logged in, go to dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        
        return $this->view('auth/login', [], 'auth');
    }

    public function authenticate()
    {
        $request = new Request();
        $body = $request->getBody();
        
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        $db = Database::getInstance();
        
        // 1. Fetch User and Settings
        $stmt = $db->query("SELECT * FROM users WHERE username = ?", [$username]);
        $user = $stmt->fetch();
        
        // Fetch Security Settings
        $settings = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'security'")->fetchAll();
        $config = [];
        foreach($settings as $s) $config[$s['setting_key']] = $s['setting_value'];
        
        $maxAttempts = intval($config['security_max_attempts'] ?? 5);
        $lockoutTime = intval($config['security_lockout_time'] ?? 30); // Minutes

        // 2. Check if Locked
        if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $minutesLeft = ceil((strtotime($user['locked_until']) - time()) / 60);
            return $this->view('auth/login', ['error' => "Account locked due to too many failed attempts. Try again in $minutesLeft minutes."], 'auth');
        }

        // 3. Verify Credentials
        if ($user && $user['status'] === 'active' && password_verify($password, $user['password_hash'])) {
            // Success
            
            // Reset failed attempts
            $db->query("UPDATE users SET failed_login_attempts = 0, locked_until = NULL WHERE id = ?", [$user['id']]);
            
            // Check Password Expiry
            $expiryDays = intval($config['security_password_expiry'] ?? 90);
            $lastChange = strtotime($user['last_password_change'] ?? $user['created_at']);
            $daysSinceChange = floor((time() - $lastChange) / (60 * 60 * 24));
            
            if ($daysSinceChange > $expiryDays) {
                // Determine logic for expired password - for now, just flash a message or similar
                // ideally redirect to /profile/change-password with a 'must_change' flag
                // keeping simple for now:
                $_SESSION['password_expired'] = true; 
            }

            Auth::login($user);
            $this->redirect('/dashboard');
        } else {
            // Failure
            if ($user) {
                // Increment failed attempts
                $attempts = $user['failed_login_attempts'] + 1;
                $updateSql = "UPDATE users SET failed_login_attempts = ?";
                $params = [$attempts];
                
                // Check if should lock
                if ($attempts >= $maxAttempts) {
                    $lockUntil = date('Y-m-d H:i:s', strtotime("+$lockoutTime minutes"));
                    $updateSql .= ", locked_until = ?";
                    $params[] = $lockUntil;
                    $errorMsg = "Account locked for $lockoutTime minutes.";
                } else {
                    $remaining = $maxAttempts - $attempts;
                    $errorMsg = "Invalid credentials. $remaining attempts remaining.";
                }
                
                $updateSql .= " WHERE id = ?";
                $params[] = $user['id'];
                
                $db->query($updateSql, $params);
            } else {
                $errorMsg = "Invalid credentials.";
            }

            return $this->view('auth/login', ['error' => $errorMsg], 'auth');
        }
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/login');
    }

    // --- Biometric Auth (simplified) ---

    public function biometricRegisterOptions()
    {
        // 1. Check if user is logged in
        if (!Auth::check()) {
            http_response_code(401);
            exit;
        }

        $user_id = Auth::id();
        $user_name = $_SESSION['username'];

        // 2. Generate Options
        // Fix: Use base64_encode for frontend atob()
        $challenge = base64_encode(random_bytes(32));
        $_SESSION['webauthn_challenge'] = $challenge;

        // Fix: Strip port from HTTP_HOST for RP ID
        $rpId = explode(':', $_SERVER['HTTP_HOST'])[0];

        $options = [
            'challenge' => $challenge,
            'rp' => ['name' => 'Supermarket OS', 'id' => $rpId],
            'user' => [
                'id' => base64_encode((string)$user_id), // Fix: Base64 encode ID
                'name' => $user_name,
                'displayName' => $_SESSION['full_name']
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7], // ES256
                ['type' => 'public-key', 'alg' => -257] // RS256
            ],
            'timeout' => 60000,
            'attestation' => 'none'
        ];

        $this->json($options);
    }

    public function biometricRegister()
    {
        if (!Auth::check()) {
            http_response_code(401);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $credentialId = $data['id'];
        $db = Database::getInstance();
        
        // Check duplication
        $exists = $db->query("SELECT id FROM user_biometrics WHERE credential_id = ?", [$credentialId])->fetch();
        if ($exists) {
            $this->json(['success' => false, 'message' => 'Device already registered']);
        }

        $db->query("INSERT INTO user_biometrics (user_id, credential_id, label) VALUES (?, ?, ?)", 
            [Auth::id(), $credentialId, 'My Device (' . date('M d') . ')']);

        $this->json(['success' => true]);
    }

    public function biometricLoginOptions()
    {
        // Fix: Use base64_encode
        $challenge = base64_encode(random_bytes(32));
        $_SESSION['webauthn_challenge'] = $challenge;

        // Fix: Strip port from HTTP_HOST
        $rpId = explode(':', $_SERVER['HTTP_HOST'])[0];

        $options = [
            'challenge' => $challenge,
            'rpId' => $rpId,
            'allowCredentials' => [], // Allow any registered credential for this RP
            'timeout' => 60000,
        ];

        $this->json($options);
    }

    public function biometricVerify()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $credentialId = $data['id'];

        // 1. Find User by Credential ID
        $db = Database::getInstance();
        $record = $db->query("SELECT * FROM user_biometrics WHERE credential_id = ?", [$credentialId])->fetch();

        if (!$record) {
            $this->json(['success' => false, 'message' => 'Device not recognized']);
        }

        // 2. Login User
        $user = $db->query("SELECT * FROM users WHERE id = ?", [$record['user_id']])->fetch();
        Auth::login($user);

        $this->json(['success' => true, 'redirect' => '/dashboard']);
    }
}
