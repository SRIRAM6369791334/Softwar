<?php

namespace App\Core;

/**
 * Feature Switcher [#103]
 * Manages dynamic feature toggles.
 */
class FeatureSwitcher
{
    private static $cache = [];

    public static function isEnabled(string $feature): bool
    {
        if (isset(self::$cache[$feature])) {
            return self::$cache[$feature];
        }

        $db = Database::getInstance();
        $setting = $db->query(
            "SELECT setting_value FROM system_settings WHERE setting_key = ?",
            ["feature_{$feature}"]
        )->fetch();

        $enabled = $setting && ($setting['setting_value'] === 'true' || $setting['setting_value'] === '1');
        self::$cache[$feature] = $enabled;
        return $enabled;
    }

    public static function enable(string $feature): void
    {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO system_settings (setting_key, setting_value, data_type) 
             VALUES (?, 'true', 'boolean') ON DUPLICATE KEY UPDATE setting_value = 'true'",
            ["feature_{$feature}"]
        );
        self::$cache[$feature] = true;
    }

    public static function disable(string $feature): void
    {
        $db = Database::getInstance();
        $db->query(
            "UPDATE system_settings SET setting_value = 'false' WHERE setting_key = ?",
            ["feature_{$feature}"]
        );
        self::$cache[$feature] = false;
    }
}
