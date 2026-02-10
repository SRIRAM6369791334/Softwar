<?php
/**
 * Migration for Phase 9: Vendor Portal
 * Creates vendors, purchase_orders, and purchase_order_items tables
 */

require __DIR__ . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

try {
    $pdo->beginTransaction();

    echo "Creating vendors table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `vendors` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(150) NOT NULL,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `password_hash` VARCHAR(255) NOT NULL,
            `phone` VARCHAR(20) NULL,
            `address` TEXT NULL,
            `tin_no` VARCHAR(50) NULL,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    echo "Creating purchase_orders table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `purchase_orders` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `vendor_id` INT NOT NULL,
            `branch_id` INT NOT NULL,
            `order_no` VARCHAR(50) NOT NULL UNIQUE,
            `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0,
            `status` ENUM('pending', 'ordered', 'partially_delivered', 'delivered', 'cancelled') DEFAULT 'pending',
            `delivery_schedule` DATE NULL,
            `invoice_pdf` VARCHAR(255) NULL,
            `created_by` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`id`),
            FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
            FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
        ) ENGINE=InnoDB;
    ");

    echo "Creating purchase_order_items table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `purchase_order_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `po_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `qty` DECIMAL(10,3) NOT NULL,
            `estimated_price` DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (`po_id`) REFERENCES `purchase_orders`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
        ) ENGINE=InnoDB;
    ");

    echo "Seeding test vendor...\n";
    $testPassword = password_hash('vendor123', PASSWORD_BCRYPT);
    $pdo->exec("
        INSERT IGNORE INTO `vendors` (`name`, `email`, `password_hash`, `phone`) 
        VALUES ('Global Suppliers', 'supplier@example.com', '$testPassword', '9876543210')
    ");

    echo "Migration complete!\n";
    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Migration failed: " . $e->getMessage());
}
