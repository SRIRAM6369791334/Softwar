<?php
/**
 * Migration for Phase 10: Advanced Vendor Portal Features
 */

require __DIR__ . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

try {
    $pdo->beginTransaction();

    echo "Adding new columns to purchase_orders...\n";
    $pdo->exec("
        ALTER TABLE `purchase_orders` 
        ADD COLUMN `backorder_status` ENUM('all_active', 'partial_backorder', 'full_backorder') DEFAULT 'all_active',
        ADD COLUMN `grn_signature` TEXT NULL,
        ADD COLUMN `grn_photo` VARCHAR(255) NULL;
    ");

    echo "Creating vendor_quotations table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `vendor_quotations` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `vendor_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `proposed_price` DECIMAL(10,2) NOT NULL,
            `current_price` DECIMAL(10,2) NOT NULL,
            `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            `admin_note` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`id`),
            FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
        ) ENGINE=InnoDB;
    ");

    echo "Creating vendor_ledger table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `vendor_ledger` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `vendor_id` INT NOT NULL,
            `po_id` INT NULL,
            `type` ENUM('credit', 'debit') NOT NULL, -- credit = we owe, debit = we paid
            `amount` DECIMAL(10,2) NOT NULL,
            `description` VARCHAR(255) NULL,
            `reference_no` VARCHAR(100) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`id`),
            FOREIGN KEY (`po_id`) REFERENCES `purchase_orders`(`id`)
        ) ENGINE=InnoDB;
    ");

    echo "Creating vendor_broadcasts table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `vendor_broadcasts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(200) NOT NULL,
            `message` TEXT NOT NULL,
            `created_by` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
        ) ENGINE=InnoDB;
    ");

    echo "Migration Phase 10 complete!\n";
    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Migration failed: " . $e->getMessage());
}
