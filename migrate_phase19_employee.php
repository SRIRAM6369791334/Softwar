<?php
/**
 * Migration for Phase 19: Employee Management Suite
 * Tables: attendance_logs, employee_messages, employee_shifts, employee_leaves
 */

require __DIR__ . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

try {
    $pdo->beginTransaction();

    // 1. Attendance Logs
    echo "Creating attendance_logs table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `attendance_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `clock_in` TIMESTAMP NULL,
            `clock_out` TIMESTAMP NULL,
            `total_hours` DECIMAL(5,2) DEFAULT 0,
            `status` ENUM('present', 'absent', 'half_day', 'late') DEFAULT 'present',
            `date` DATE NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
            INDEX `idx_user_date` (`user_id`, `date`)
        ) ENGINE=InnoDB;
    ");

    // 2. Employee Messages (Internal Messenger)
    echo "Creating employee_messages table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `employee_messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `sender_id` INT NOT NULL,
            `target_role_id` INT NULL, -- NULL = All
            `title` VARCHAR(150) NOT NULL,
            `message` TEXT NOT NULL,
            `is_urgent` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`)
        ) ENGINE=InnoDB;
    ");

    // 3. Employee Shifts (Rostering)
    echo "Creating employee_shifts table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `employee_shifts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `start_time` DATETIME NOT NULL,
            `end_time` DATETIME NOT NULL,
            `type` ENUM('morning', 'afternoon', 'night', 'general') DEFAULT 'general',
            `notes` VARCHAR(255) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ) ENGINE=InnoDB;
    ");

    // 4. Employee Leaves
    echo "Creating employee_leaves table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `employee_leaves` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `type` ENUM('sick', 'casual', 'earned', 'unpaid') NOT NULL,
            `start_date` DATE NOT NULL,
            `end_date` DATE NOT NULL,
            `days` DECIMAL(5,1) NOT NULL,
            `reason` TEXT NOT NULL,
            `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            `approved_by` INT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
            FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`)
        ) ENGINE=InnoDB;
    ");

    echo "Migration Phase 19 complete!\n";
    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Migration failed: " . $e->getMessage());
}
