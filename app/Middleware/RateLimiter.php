<?php

namespace App\Middleware;

/**
 * Rate Limiting Middleware
 * Prevents API abuse and brute force attacks
 */
class RateLimiter
{
    private static string $storageFile = __DIR__ . '/../../storage/rate_limits.json';
    
    /**
     * Check if request should be rate limited
     * 
     * @param string $identifier Unique identifier (IP, user ID, etc.)
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $windowSeconds Time window in seconds
     * @return bool True if rate limit exceeded
     */
    public static function isRateLimited(string $identifier, int $maxAttempts = 60, int $windowSeconds = 60): bool
    {
        $data = self::loadData();
        $now = time();
        
        // Clean up old entries
        $data = array_filter($data, fn($entry) => ($now - $entry['timestamp']) < $windowSeconds);
        
        // Get attempts for this identifier
        $key = self::getKey($identifier);
        if (!isset($data[$key])) {
            $data[$key] = ['count' => 0, 'timestamp' => $now];
        }
        
        // Check if within time window
        if (($now - $data[$key]['timestamp']) >= $windowSeconds) {
            // Reset window
            $data[$key] = ['count' => 1, 'timestamp' => $now];
            self::saveData($data);
            return false;
        }
        
        // Increment counter
        $data[$key]['count']++;
        self::saveData($data);
        
        return $data[$key]['count'] > $maxAttempts;
    }
    
    /**
     * Record failed login attempt
     */
    public static function recordFailedLogin(string $identifier): void
    {
        self::increment("login_fail_$identifier");
    }
    
    /**
     * Check if login is blocked due to too many failures
     */
    public static function isLoginBlocked(string $identifier, int $maxAttempts = 5, int $blockSeconds = 900): bool
    {
        return self::isRateLimited("login_fail_$identifier", $maxAttempts, $blockSeconds);
    }
    
    /**
     * Clear rate limit for identifier
     */
    public static function clear(string $identifier): void
    {
        $data = self::loadData();
        $key = self::getKey($identifier);
        unset($data[$key]);
        self::saveData($data);
    }
    
    /**
     * Increment counter for identifier
     */
    private static function increment(string $identifier): void
    {
        $data = self::loadData();
        $key = self::getKey($identifier);
        $now = time();
        
        if (!isset($data[$key])) {
            $data[$key] = ['count' => 1, 'timestamp' => $now];
        } else {
            $data[$key]['count']++;
            $data[$key]['timestamp'] = $now;
        }
        
        self::saveData($data);
    }
    
    /**
     * Generate storage key
     */
    private static function getKey(string $identifier): string
    {
        return md5($identifier);
    }
    
    /**
     * Load rate limit data from storage
     */
    private static function loadData(): array
    {
        if (!file_exists(self::$storageFile)) {
            self::ensureStorageDirectory();
            return [];
        }
        
        $content = file_get_contents(self::$storageFile);
        return json_decode($content, true) ?: [];
    }
    
    /**
     * Save rate limit data to storage
     */
    private static function saveData(array $data): void
    {
        self::ensureStorageDirectory();
        file_put_contents(self::$storageFile, json_encode($data));
    }
    
    /**
     * Ensure storage directory exists
     */
    private static function ensureStorageDirectory(): void
    {
        $dir = dirname(self::$storageFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    /**
     * Get client IP address
     */
    public static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
}
