<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting Phase 22 Migration: Admin System Control...\n";

try {
    // 1. Create 'settings' table
    echo "Creating 'settings' table...\n";
    $pdo->exec("DROP TABLE IF EXISTS settings");
    $sql = "CREATE TABLE settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) DEFAULT 'general',
        input_type VARCHAR(20) DEFAULT 'text',
        label VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
    echo "Table 'settings' created.\n";

    // 2. Seed Default Settings
    echo "Seeding default settings...\n";
    $defaults = [
        // Branding
        ['theme_primary_color', '#0d6efd', 'branding', 'color', 'Primary Color'],
        ['theme_accent_color', '#6610f2', 'branding', 'color', 'Accent Color'],
        ['theme_sidebar_color', '#212529', 'branding', 'color', 'Sidebar Background'],
        
        // Store Meta
        ['store_name', 'Supermarket OS', 'store', 'text', 'Store Name'],
        ['store_address', '123 Main St, City', 'store', 'textarea', 'Address'],
        ['store_phone', '+1 234 567 890', 'store', 'text', 'Phone Number'],
        ['store_gstin', '', 'store', 'text', 'GSTIN / Tax ID'],
        
        // System
        ['maintenance_mode', '0', 'system', 'boolean', 'Maintenance Mode'],
        ['debug_mode', '0', 'system', 'boolean', 'Debug Mode'],
        
        // Email (SMTP)
        ['smtp_host', 'smtp.example.com', 'email', 'text', 'SMTP Host'],
        ['smtp_port', '587', 'email', 'number', 'SMTP Port'],
        ['smtp_user', 'user@example.com', 'email', 'text', 'SMTP Username'],
        ['smtp_pass', '', 'email', 'password', 'SMTP Password'],
        ['smtp_encryption', 'tls', 'email', 'select', 'Encryption (tls/ssl)'],
        
        // Invoices
        ['invoice_footer_text', 'Thank you for your business!', 'invoice', 'textarea', 'Invoice Footer'],
        ['invoice_show_logo', '1', 'invoice', 'boolean', 'Show Logo on Invoice'],
    ];

    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group, input_type, label) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE label=VALUES(label)");
    
    foreach ($defaults as $setting) {
        $stmt->execute($setting);
    }
    echo "Default settings seeded.\n";

} catch (PDOException $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Phase 22 Migration Complete!\n";
