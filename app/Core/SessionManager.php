<?php

namespace App\Core;

/**
 * Session Manager
 * Handles secure session management with timeout and hijacking protection
 */
class SessionManager
{
    private const SESSION_TIMEOUT = 1800; // 30 minutes
    private const SESSION_REGENERATE_INTERVAL = 300; // 5 minutes
    
    /**
     * Start secure session
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure session configuration
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', '1'); // HTTPS only
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', '1');
            
            session_start();
            
            // Initialize session security
            if (!isset($_SESSION['_security'])) {
                self::initializeSecurity();
            }
            
            // Check for session hijacking
            if (!self::validateSession()) {
                self::destroy();
                throw new \Exception('Session validation failed');
            }
            
            // Check for timeout
            if (self::isExpired()) {
                self::destroy();
                throw new \Exception('Session expired');
            }
            
            // Regenerate session ID periodically
            if (self::shouldRegenerate()) {
                self::regenerate();
            }
            
            // Update last activity
            $_SESSION['_security']['last_activity'] = time();
        }
    }
    
    /**
     * Initialize session security data
     */
    private static function initializeSecurity(): void
    {
        $_SESSION['_security'] = [
            'created' => time(),
            'last_activity' => time(),
            'last_regeneration' => time(),
            'ip' => self::getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'fingerprint' => self::generateFingerprint(),
        ];
    }
    
    /**
     * Validate session against hijacking
     */
    private static function validateSession(): bool
    {
        if (!isset($_SESSION['_security'])) {
            return false;
        }
        
        $security = $_SESSION['_security'];
        
        // Check IP address (strict mode - can be relaxed for mobile users)
        if ($security['ip'] !== self::getClientIp()) {
            error_log('Session hijacking attempt: IP mismatch');
            return false;
        }
        
        // Check user agent
        if ($security['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            error_log('Session hijacking attempt: User agent mismatch');
            return false;
        }
        
        // Check fingerprint
        if ($security['fingerprint'] !== self::generateFingerprint()) {
            error_log('Session hijacking attempt: Fingerprint mismatch');
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if session has expired
     */
    private static function isExpired(): bool
    {
        if (!isset($_SESSION['_security']['last_activity'])) {
            return true;
        }
        
        $inactive = time() - $_SESSION['_security']['last_activity'];
        return $inactive > self::SESSION_TIMEOUT;
    }
    
    /**
     * Check if session ID should be regenerated
     */
    private static function shouldRegenerate(): bool
    {
        if (!isset($_SESSION['_security']['last_regeneration'])) {
            return true;
        }
        
        $elapsed = time() - $_SESSION['_security']['last_regeneration'];
        return $elapsed > self::SESSION_REGENERATE_INTERVAL;
    }
    
    /**
     * Regenerate session ID
     */
    public static function regenerate(): void
    {
        if (!headers_sent()) {
            session_regenerate_id(true);
            $_SESSION['_security']['last_regeneration'] = time();
        }
    }
    
    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            
            // Delete session cookie
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            
            session_destroy();
        }
    }
    
    /**
     * Get client IP address
     */
    private static function getClientIp(): string
    {
        // Use the RateLimiter's IP detection
        if (class_exists('App\Middleware\RateLimiter')) {
            return \App\Middleware\RateLimiter::getClientIp();
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Generate session fingerprint
     */
    private static function generateFingerprint(): string
    {
        $data = [
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
        ];
        
        return hash('sha256', implode('|', $data));
    }
    
    /**
     * Extend session timeout (e.g., for "remember me")
     */
    public static function extendTimeout(int $seconds): void
    {
        if (isset($_SESSION['_security'])) {
            $_SESSION['_security']['last_activity'] = time() + $seconds;
        }
    }
    
    /**
     * Get session timeout remaining
     */
    public static function getTimeoutRemaining(): int
    {
        if (!isset($_SESSION['_security']['last_activity'])) {
            return 0;
        }
        
        $remaining = self::SESSION_TIMEOUT - (time() - $_SESSION['_security']['last_activity']);
        return max(0, $remaining);
    }
}
