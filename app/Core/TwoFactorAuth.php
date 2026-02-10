<?php

namespace App\Core;

/**
 * Two-Factor Authentication Manager
 * Handles TOTP-based 2FA for admin accounts
 */
class TwoFactorAuth
{
    /**
     * Generate secret key for 2FA
     */
    public static function generateSecret(): string
    {
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        
        for ($i = 0; $i < 16; $i++) {
            $secret .= $validChars[random_int(0, 31)];
        }
        
        return $secret;
    }
    
    /**
     * Generate QR code URL for Google Authenticator
     */
    public static function getQrCodeUrl(string $secret, string $email, string $issuer = 'Supermarket OS'): string
    {
        $label = urlencode($issuer . ':' . $email);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
        ]);
        
        $otpauthUrl = "otpauth://totp/{$label}?{$params}";
        
        // Use Google Charts API for QR code generation
        return 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . urlencode($otpauthUrl);
    }
    
    /**
     * Verify TOTP code
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $timestamp = floor(time() / 30);
        
        // Check current timestamp and adjacent ones (to account for time drift)
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            if (self::getCode($secret, $timestamp + $i) === $code) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate TOTP code for given secret and timestamp
     */
    private static function getCode(string $secret, int $timestamp): string
    {
        // Decode base32 secret
        $key = self::base32Decode($secret);
        
        // Pack timestamp as 64-bit big-endian
        $time = pack('N*', 0) . pack('N*', $timestamp);
        
        // HMAC-SHA1
        $hash = hash_hmac('sha1', $time, $key, true);
        
        // Dynamic truncation
        $offset = ord($hash[19]) & 0x0f;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        
        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Decode base32 string
     */
    private static function base32Decode(string $input): string
    {
        $map = [
            'A' => 0,  'B' => 1,  'C' => 2,  'D' => 3,  'E' => 4,  'F' => 5,
            'G' => 6,  'H' => 7,  'I' => 8,  'J' => 9,  'K' => 10, 'L' => 11,
            'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17,
            'S' => 18, 'T' => 19, 'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
            'Y' => 24, 'Z' => 25, '2' => 26, '3' => 27, '4' => 28, '5' => 29,
            '6' => 30, '7' => 31
        ];
        
        $input = strtoupper($input);
        $output = '';
        $buffer = 0;
        $bitsLeft = 0;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            
            if (!isset($map[$char])) {
                continue;
            }
            
            $buffer = ($buffer << 5) | $map[$char];
            $bitsLeft += 5;
            
            if ($bitsLeft >= 8) {
                $output .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
                $bitsLeft -= 8;
            }
        }
        
        return $output;
    }
    
    /**
     * Enable 2FA for user
     */
    public static function enableForUser(int $userId, string $secret): bool
    {
        $db = Database::getInstance();
        
        try {
            $db->query(
                "UPDATE users SET two_factor_secret = ?, two_factor_enabled = 1 WHERE id = ?",
                [$secret, $userId]
            );
            
            return true;
        } catch (\Exception $e) {
            error_log("Failed to enable 2FA for user $userId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Disable 2FA for user
     */
    public static function disableForUser(int $userId): bool
    {
        $db = Database::getInstance();
        
        try {
            $db->query(
                "UPDATE users SET two_factor_secret = NULL, two_factor_enabled = 0 WHERE id = ?",
                [$userId]
            );
            
            return true;
        } catch (\Exception $e) {
            error_log("Failed to disable 2FA for user $userId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user has 2FA enabled
     */
    public static function isEnabledForUser(int $userId): bool
    {
        $db = Database::getInstance();
        
        $user = $db->query(
            "SELECT two_factor_enabled FROM users WHERE id = ?",
            [$userId]
        )->fetch();
        
        return ($user['two_factor_enabled'] ?? 0) == 1;
    }
    
    /**
     * Get user's 2FA secret
     */
    public static function getUserSecret(int $userId): ?string
    {
        $db = Database::getInstance();
        
        $user = $db->query(
            "SELECT two_factor_secret FROM users WHERE id = ?",
            [$userId]
        )->fetch();
        
        return $user['two_factor_secret'] ?? null;
    }
    
    /**
     * Generate backup codes for user
     */
    public static function generateBackupCodes(int $count = 10): array
    {
        $codes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $code = '';
            for ($j = 0; $j < 8; $j++) {
                $code .= random_int(0, 9);
            }
            $codes[] = $code;
        }
        
        return $codes;
    }
    
    /**
     * Store backup codes for user
     */
    public static function storeBackupCodes(int $userId, array $codes): bool
    {
        $db = Database::getInstance();
        
        // Hash the codes before storing
        $hashedCodes = array_map(fn($code) => password_hash($code, PASSWORD_BCRYPT), $codes);
        
        try {
            $db->query(
                "UPDATE users SET two_factor_backup_codes = ? WHERE id = ?",
                [json_encode($hashedCodes), $userId]
            );
            
            return true;
        } catch (\Exception $e) {
            error_log("Failed to store backup codes for user $userId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify backup code
     */
    public static function verifyBackupCode(int $userId, string $code): bool
    {
        $db = Database::getInstance();
        
        $user = $db->query(
            "SELECT two_factor_backup_codes FROM users WHERE id = ?",
            [$userId]
        )->fetch();
        
        if (empty($user['two_factor_backup_codes'])) {
            return false;
        }
        
        $hashedCodes = json_decode($user['two_factor_backup_codes'], true);
        
        foreach ($hashedCodes as $index => $hashedCode) {
            if (password_verify($code, $hashedCode)) {
                // Remove used code
                unset($hashedCodes[$index]);
                $db->query(
                    "UPDATE users SET two_factor_backup_codes = ? WHERE id = ?",
                    [json_encode(array_values($hashedCodes)), $userId]
                );
                
                return true;
            }
        }
        
        return false;
    }
}
