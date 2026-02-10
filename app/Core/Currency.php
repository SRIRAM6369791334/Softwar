<?php

namespace App\Core;

/**
 * Currency Manager [#99]
 * Handles monetary formatting and currency settings.
 */
class Currency
{
    private static $code;
    private static $symbol;

    private static function load(): void
    {
        if (self::$code) return;
        
        $db = Database::getInstance();
        $settings = $db->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'currency_%'")->fetchAll();
        
        $map = [];
        foreach ($settings as $s) $map[$s['setting_key']] = $s['setting_value'];
        
        self::$code = $map['currency_code'] ?? 'USD';
        self::$symbol = $map['currency_symbol'] ?? '$';
    }

    public static function format($amount): string
    {
        self::load();
        return self::$symbol . number_format((float)$amount, 2);
    }

    public static function getCode(): string
    {
        self::load();
        return self::$code;
    }
}
