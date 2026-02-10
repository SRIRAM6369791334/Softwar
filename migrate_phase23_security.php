<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting Phase 23 Migration: Security & Automation Settings...\n";

try {
    // 1. Add new security & automation settings
    echo "Seeding security & automation settings...\n";
    $newSettings = [
        // Security Policy
        ['security_password_expiry', '90', 'security', 'number', 'Password Expiry (Days)'],
        ['security_max_attempts', '5', 'security', 'number', 'Max Login Attempts'],
        ['security_lockout_time', '30', 'security', 'number', 'Lockout Duration (Mins)'],
        ['security_redact_data', '0', 'security', 'boolean', 'Mask Sensitive Customer Data'],
        
        // Automation / Email Templates
        ['email_welcome_subject', 'Welcome to Supermarket OS!', 'automation', 'text', 'Welcome Email Subject'],
        ['email_welcome_body', 'Hi {name}, Welcome to our team. Your login is {email}.', 'automation', 'textarea', 'Welcome Email Body'],
        ['email_low_stock_alert', '1', 'automation', 'boolean', 'Enable Low Stock Emails'],
    ];

    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group, input_type, label) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE label=VALUES(label), group=VALUES(setting_group)");
    
    foreach ($newSettings as $setting) {
        // Adjust array structure if necessary based on previous migration
        // Previous migration used: setting_key, setting_value, setting_group, input_type, label
        $stmt->execute($setting);
    }
    echo "Security settings seeded.\n";

    // 2. Create 'action_logs' table for Super Admin Logging (Immutable)
    echo "Creating 'action_logs' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS action_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action_type VARCHAR(50) NOT NULL, -- e.g. 'UPDATE_SETTING', 'LOGIN_FAILED'
        target_table VARCHAR(50),
        target_id INT,
        old_value TEXT,
        new_value TEXT,
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id),
        INDEX (action_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
    echo "Table 'action_logs' created.\n";

} catch (PDOException $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Phase 23 Migration Complete!\n";
