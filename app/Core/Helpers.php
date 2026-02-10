<?php

namespace App\Core;

class Helpers
{
    private static $settings = null;

    public static function mask(string $value, string $type = 'text'): string
    {
        // 1. Check if user is Admin (Role 1) - Admins see everything
        if (Auth::check() && Auth::hasRole(1)) {
            return $value;
        }

        // 2. Check if Redaction is Enabled
        if (self::$settings === null) {
            $db = Database::getInstance();
            // Fetch setting directly
            $stmt = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'security_redact_data'");
            $res = $stmt->fetch();
            self::$settings['redact'] = $res ? ($res['setting_value'] == '1') : false;
        }

        if (!self::$settings['redact']) {
            return $value;
        }

        // 3. Apply Masking
        if ($type === 'email') {
            $parts = explode('@', $value);
            if (count($parts) === 2) {
                return substr($parts[0], 0, 2) . '****@' . $parts[1];
            }
        } elseif ($type === 'phone') {
            return substr($value, 0, 3) . '****' . substr($value, -2);
        }

        // Default: Show first 2 chars
        return substr($value, 0, 2) . '******';
    }
    
    public static function formatMoney($amount)
    {
        return '$' . number_format($amount, 2);
    }
}
