<?php
/**
 * Migration for Phase 21: Advanced Employee Features
 * Adds support for Grace Period, Overtime, and Open Shifts
 */

require __DIR__ . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

try {
    $pdo->beginTransaction();

    echo "Starting Phase 21 Migration...\n";

    // 1. Settings Table (Ensure it exists)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(50) UNIQUE NOT NULL,
            `value` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    // Insert default grace period if not exists
    $stmt = $pdo->query("SELECT id FROM settings WHERE `key` = 'attendance_grace_period'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO settings (`key`, `value`) VALUES ('attendance_grace_period', '15')");
        echo "Added setting: attendance_grace_period = 15\n";
    }

    // 2. Attendance Logs - Overtime Columns
    $cols = $pdo->query("SHOW COLUMNS FROM attendance_logs LIKE 'is_overtime'")->fetchAll();
    if (count($cols) == 0) {
        $pdo->exec("
            ALTER TABLE `attendance_logs` 
            ADD COLUMN `is_overtime` TINYINT(1) DEFAULT 0,
            ADD COLUMN `overtime_minutes` INT DEFAULT 0,
            ADD COLUMN `manager_approval_status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
        ");
        echo "Added overtime columns to attendance_logs\n";
    }

    // 3. Employee Shifts - Open Shift Columns
    $cols = $pdo->query("SHOW COLUMNS FROM employee_shifts LIKE 'is_open'")->fetchAll();
    if (count($cols) == 0) {
        $pdo->exec("
            ALTER TABLE `employee_shifts` 
            MODIFY COLUMN `user_id` INT NULL, -- Allow NULL for open shifts
            ADD COLUMN `is_open` TINYINT(1) DEFAULT 0,
            ADD COLUMN `max_claimants` INT DEFAULT 1
        ");
        echo "Modified employee_shifts for Open Shifts support\n";
    }

    echo "Migration Phase 21 complete!\n";
    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Migration failed: " . $e->getMessage());
}
