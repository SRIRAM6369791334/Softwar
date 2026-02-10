<?php

namespace App\Core;

/**
 * Security Utilities
 * Handles CSRF protection, XSS prevention, and other security features
 */
class Security
{
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get CSRF token from request
     */
    public static function getCsrfTokenFromRequest(): ?string
    {
        // Check POST data
        if (isset($_POST['csrf_token'])) {
            return $_POST['csrf_token'];
        }

        // Check headers (for AJAX)
        $headers = getallheaders();
        if (isset($headers['X-CSRF-Token'])) {
            return $headers['X-CSRF-Token'];
        }

        return null;
    }

    /**
     * Escape output for XSS protection
     */
    public static function escape($value, $encoding = 'UTF-8')
    {
        if ($value === null) {
            return '';
        }

        if (is_array($value)) {
            return array_map([self::class, 'escape'], $value);
        }

        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, $encoding);
    }

    /**
     * Generate secure random string
     */
    public static function randomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Hash password securely
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Sanitize filename
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove any path components
        $filename = basename($filename);
        
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        return $filename;
    }

    /**
     * Encrypt sensitive data (PII) [#83]
     */
    public static function encryptData(string $data): string
    {
        $key = \App\Core\Env::get('APP_KEY', 'default_secret_key_32_chars');
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $encrypted . $iv, $key, true);
        return base64_encode($iv . $hmac . $encrypted);
    }

    /**
     * Decrypt sensitive data
     */
    public static function decryptData(string $base64Data): ?string
    {
        $key = \App\Core\Env::get('APP_KEY', 'default_secret_key_32_chars');
        $data = base64_decode($base64Data);
        $ivLen = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLen);
        $hmac = substr($data, $ivLen, 32);
        $encrypted = substr($data, $ivLen + 32);
        
        $calculatedHmac = hash_hmac('sha256', $encrypted . $iv, $key, true);
        if (!hash_equals($hmac, $calculatedHmac)) {
            return null; // Integrity check failed
        }
        
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Generate secure headers
     */
    public static function setSecurityHeaders(): void
    {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:");
    }
}
