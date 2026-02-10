<?php
/**
 * Phase 26: Advanced Employee Features Migration
 * - Grace Period Management
 * - Overtime Tracking
 * - Open Shifts Marketplace
 * - Enhanced Timesheets
 */

require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "=== Phase 26: Advanced Employee Features Migration ===\n\n";

try {
    // 1. Create shift_templates table
    echo "Creating shift_templates table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS shift_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            break_minutes INT DEFAULT 0,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 2. Create overtime_records table
    echo "Creating overtime_records table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS overtime_records (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            attendance_id INT,
            date DATE NOT NULL,
            overtime_hours DECIMAL(5,2) NOT NULL,
            overtime_rate DECIMAL(5,2) DEFAULT 1.5,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            reason TEXT,
            approved_by INT,
            approved_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 3. Create grace_period_logs table
    echo "Creating grace_period_logs table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS grace_period_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            attendance_id INT,
            scheduled_time TIME NOT NULL,
            actual_time TIME NOT NULL,
            grace_minutes INT NOT NULL,
            date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 4. Update employee_shifts table (if it exists)
    echo "Checking employee_shifts table...\n";
    $tables = $pdo->query("SHOW TABLES LIKE 'employee_shifts'")->fetchAll();
    if (count($tables) > 0) {
        echo "Updating employee_shifts table...\n";
        $pdo->exec("
            ALTER TABLE employee_shifts 
            ADD COLUMN IF NOT EXISTS is_open TINYINT(1) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS claimed_by INT NULL,
            ADD COLUMN IF NOT EXISTS claimed_at TIMESTAMP NULL,
            ADD COLUMN IF NOT EXISTS overtime_hours DECIMAL(5,2) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS grace_used TINYINT(1) DEFAULT 0
        ");
    } else {
        echo "⚠️ employee_shifts table not found. Run migrate_phase19_employee.php first.\n";
    }

    // 5. Update attendance_logs table (if it exists)
    echo "Checking attendance_logs table...\n";
    $tables = $pdo->query("SHOW TABLES LIKE 'attendance_logs'")->fetchAll();
    if (count($tables) > 0) {
        echo "Updating attendance_logs table...\n";
        $pdo->exec("
            ALTER TABLE attendance_logs 
            ADD COLUMN IF NOT EXISTS grace_minutes INT DEFAULT 0,
            ADD COLUMN IF NOT EXISTS overtime_approved TINYINT(1) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS overtime_rate DECIMAL(5,2) DEFAULT 1.5,
            ADD COLUMN IF NOT EXISTS notes TEXT
        ");
    } else {
        echo "⚠️ attendance_logs table not found. Run migrate_phase19_employee.php first.\n";
    }

    // 6. Update users table
    echo "Updating users table...\n";
    $pdo->exec("
        ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS grace_period_minutes INT DEFAULT 15,
        ADD COLUMN IF NOT EXISTS overtime_eligible TINYINT(1) DEFAULT 1,
        ADD COLUMN IF NOT EXISTS max_grace_uses_per_month INT DEFAULT 5
    ");

    // 7. Seed default shift templates
    echo "Seeding shift templates...\n";
    $pdo->exec("
        INSERT IGNORE INTO shift_templates (id, name, start_time, end_time, break_minutes, description) VALUES
        (1, 'Morning Shift', '06:00:00', '14:00:00', 30, 'Standard morning shift with 30-min break'),
        (2, 'Day Shift', '09:00:00', '17:00:00', 60, 'Standard day shift with 1-hour lunch'),
        (3, 'Evening Shift', '14:00:00', '22:00:00', 30, 'Evening shift with 30-min break'),
        (4, 'Night Shift', '22:00:00', '06:00:00', 45, 'Overnight shift with 45-min break')
    ");

    // 8. Add system settings for grace/overtime
    echo "Adding system settings...\n";
    $pdo->exec("
        INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
        ('grace_period_enabled', '1', 'attendance'),
        ('grace_period_default_minutes', '15', 'attendance'),
        ('grace_period_max_monthly_uses', '5', 'attendance'),
        ('overtime_rate_standard', '1.5', 'payroll'),
        ('overtime_rate_holiday', '2.0', 'payroll'),
        ('overtime_auto_approve_threshold', '2', 'payroll'),
        ('open_shifts_enabled', '1', 'roster'),
        ('open_shifts_approval_required', '0', 'roster')
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ");

    echo "\n✅ Phase 26 Migration Complete!\n";
    echo "-----------------------------------\n";
    echo "New Tables: shift_templates, overtime_records, grace_period_logs\n";
    echo "Updated Tables: employee_shifts, employee_attendance, users\n";
    echo "System Settings: Grace period & overtime policies configured\n";

} catch (PDOException $e) {
    echo "❌ Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}
