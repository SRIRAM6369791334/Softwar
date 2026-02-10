<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;
use App\Core\Request;

class AuthController extends Controller
{
    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function isValidCredentialId(string $value): bool
    {
        // Supports standard/base64url encoding often used by WebAuthn credential IDs.
        return (bool) preg_match('/^[A-Za-z0-9+\/_\-=]+$/', $value);
    }

    private function getClientIp(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }

    private function getRpId(): string
    {
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $host = preg_replace('/:\\d+$/', '', trim($host));

        if ($host === '' || $host === null) {
            return 'localhost';
        }

        return strtolower($host);
    }

    private function getSecurityConfig(Database $db): array
    {
        $settings = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'security'")->fetchAll();
        $config = [];

        foreach ($settings as $s) {
            $config[$s['setting_key']] = $s['setting_value'];
        }

        return $config;
    }

    private function logAuthEvent(string $event, string $username, array $context = []): void
    {
        $payload = [
            'ts' => date('c'),
            'event' => $event,
            'username' => $username,
            'ip' => $this->getClientIp(),
            'context' => $context,
        ];

        @error_log('[AUTH] ' . json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, 3, APP_ROOT . '/logs/auth.log');
    }

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
        $this->ensureSessionStarted();

        $request = new Request();
        $body = $request->getBody();

        $username = trim((string) ($body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($username === '' || $password === '') {
            $this->logAuthEvent('login_rejected_empty_fields', $username);
            return $this->view('auth/login', ['error' => 'Invalid credentials.'], 'auth');
        }

        $db = Database::getInstance();
        $config = $this->getSecurityConfig($db);

        $maxAttempts = max(1, intval($config['security_max_attempts'] ?? 5));
        $lockoutTime = max(1, intval($config['security_lockout_time'] ?? 30)); // minutes

        // 1. Fetch user
        $stmt = $db->query("SELECT * FROM users WHERE username = ?", [$username]);
        $user = $stmt->fetch();

        // 2. Check lock
        if ($user && !empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $this->logAuthEvent('login_blocked_locked_account', $username, ['user_id' => $user['id']]);
            return $this->view('auth/login', ['error' => 'Invalid credentials.'], 'auth');
        }

        // 3. Verify credentials
        if ($user && ($user['status'] ?? '') === 'active' && password_verify($password, $user['password_hash'])) {
            $db->query("UPDATE users SET failed_login_attempts = 0, locked_until = NULL WHERE id = ?", [$user['id']]);

            Auth::login($user);

            $expiryDays = max(1, intval($config['security_password_expiry'] ?? 90));
            $lastChange = strtotime($user['last_password_change'] ?? $user['created_at']);
            $daysSinceChange = floor((time() - $lastChange) / (60 * 60 * 24));

            if ($daysSinceChange > $expiryDays) {
                $_SESSION['password_expired'] = true;
            }

            $this->logAuthEvent('login_success', $username, ['user_id' => $user['id']]);
            $this->redirect('/dashboard');
        }

        // 4. Failure (generic response to reduce user enumeration)
        if ($user) {
            $attempts = ((int) $user['failed_login_attempts']) + 1;
            $updateSql = "UPDATE users SET failed_login_attempts = ?";
            $params = [$attempts];

            if ($attempts >= $maxAttempts) {
                $lockUntil = date('Y-m-d H:i:s', strtotime("+$lockoutTime minutes"));
                $updateSql .= ", locked_until = ?";
                $params[] = $lockUntil;
            }

            $updateSql .= " WHERE id = ?";
            $params[] = $user['id'];
            $db->query($updateSql, $params);

            $this->logAuthEvent('login_failed', $username, ['user_id' => $user['id'], 'attempts' => $attempts]);
        } else {
            $this->logAuthEvent('login_failed_unknown_user', $username);
        }

        // tiny jitter to reduce brute-force throughput
        usleep(random_int(100000, 300000));
        return $this->view('auth/login', ['error' => 'Invalid credentials.'], 'auth');
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/login');
    }

    // --- Biometric Auth (simplified) ---

    public function biometricRegisterOptions()
    {
        $this->ensureSessionStarted();

        if (!Auth::check()) {
            http_response_code(401);
            exit;
        }

        $userId = Auth::id();
        $userName = (string) ($_SESSION['username'] ?? 'user');

        $challenge = base64_encode(random_bytes(32));
        $_SESSION['webauthn_challenge'] = $challenge;

        $options = [
            'challenge' => $challenge,
            'rp' => ['name' => 'Supermarket OS', 'id' => $this->getRpId()],
            'user' => [
                'id' => base64_encode((string) $userId),
                'name' => $userName,
                'displayName' => (string) ($_SESSION['full_name'] ?? $userName),
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],
                ['type' => 'public-key', 'alg' => -257],
            ],
            'timeout' => 60000,
            'attestation' => 'none',
        ];

        $this->json($options);
    }

    public function biometricRegister()
    {
        $this->ensureSessionStarted();

        if (!Auth::check()) {
            http_response_code(401);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            return $this->json(['success' => false, 'message' => 'Invalid request payload']);
        }

        if (empty($data['challenge']) || !hash_equals((string) ($_SESSION['webauthn_challenge'] ?? ''), (string) $data['challenge'])) {
            return $this->json(['success' => false, 'message' => 'Invalid authentication challenge']);
        }

        $credentialId = trim((string) ($data['id'] ?? ''));
        if ($credentialId === '' || strlen($credentialId) > 2048 || !$this->isValidCredentialId($credentialId)) {
            return $this->json(['success' => false, 'message' => 'Invalid credential identifier']);
        }

        $db = Database::getInstance();
        $exists = $db->query("SELECT id FROM user_biometrics WHERE credential_id = ?", [$credentialId])->fetch();
        if ($exists) {
            return $this->json(['success' => false, 'message' => 'Device already registered']);
        }

        $db->query(
            "INSERT INTO user_biometrics (user_id, credential_id, label) VALUES (?, ?, ?)",
            [Auth::id(), $credentialId, 'My Device (' . date('M d') . ')']
        );

        unset($_SESSION['webauthn_challenge']);
        $this->json(['success' => true]);
    }

    public function biometricLoginOptions()
    {
        $this->ensureSessionStarted();

        $challenge = base64_encode(random_bytes(32));
        $_SESSION['webauthn_challenge'] = $challenge;

        $options = [
            'challenge' => $challenge,
            'rpId' => $this->getRpId(),
            'allowCredentials' => [],
            'timeout' => 60000,
        ];

        $this->json($options);
    }

    public function biometricVerify()
    {
        $this->ensureSessionStarted();

        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            return $this->json(['success' => false, 'message' => 'Invalid request payload']);
        }

        if (empty($data['challenge']) || !hash_equals((string) ($_SESSION['webauthn_challenge'] ?? ''), (string) $data['challenge'])) {
            return $this->json(['success' => false, 'message' => 'Authentication challenge validation failed']);
        }

        $credentialId = trim((string) ($data['id'] ?? ''));
        if ($credentialId === '' || strlen($credentialId) > 2048 || !$this->isValidCredentialId($credentialId)) {
            return $this->json(['success' => false, 'message' => 'Invalid credential identifier']);
        }

        $db = Database::getInstance();
        $record = $db->query("SELECT * FROM user_biometrics WHERE credential_id = ?", [$credentialId])->fetch();

        if (!$record) {
            $this->logAuthEvent('biometric_login_failed_unknown_device', 'biometric');
            return $this->json(['success' => false, 'message' => 'Device not recognized']);
        }

        $user = $db->query("SELECT * FROM users WHERE id = ?", [$record['user_id']])->fetch();
        if (!$user || ($user['status'] ?? 'inactive') !== 'active') {
            $this->logAuthEvent('biometric_login_failed_inactive_user', 'biometric', ['user_id' => $record['user_id']]);
            return $this->json(['success' => false, 'message' => 'Account unavailable']);
        }

        unset($_SESSION['webauthn_challenge']);
        Auth::login($user);
        $this->logAuthEvent('biometric_login_success', (string) ($user['username'] ?? 'biometric'), ['user_id' => $user['id']]);

        $this->json(['success' => true, 'redirect' => '/dashboard']);
    }
}
