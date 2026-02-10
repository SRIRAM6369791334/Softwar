-- Supermarket OS Master Schema
-- Version: 1.0.0
-- Engine: InnoDB
-- Charset: utf8mb4

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users & Roles (Employee Management)
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO `roles` (`name`, `slug`) VALUES
('Administrator', 'admin'),
('Store Manager', 'manager'),
('Cashier', 'cashier'),
('Stock Keeper', 'inventory');

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `last_login` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB;

-- 2. Products & Inventory
CREATE TABLE IF NOT EXISTS `tax_groups` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL, -- e.g. "GST 5%"
    `percentage` DECIMAL(5,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO `tax_groups` (`name`, `percentage`) VALUES
('Exempt', 0.00), ('GST 5%', 5.00), ('GST 12%', 12.00), ('GST 18%', 18.00), ('GST 28%', 28.00);

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tax_group_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `sku` VARCHAR(50) NULL UNIQUE, -- EAN/Barcode
    `hsn_code` VARCHAR(20) NULL,
    `unit` VARCHAR(20) DEFAULT 'Nos', -- Nos, Kg, Ltr
    `min_stock_alert` INT DEFAULT 10,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`tax_group_id`) REFERENCES `tax_groups`(`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `product_batches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `batch_no` VARCHAR(50) NOT NULL,
    `expiry_date` DATE NULL,
    `purchase_price` DECIMAL(10,2) NOT NULL, -- Cost Price
    `mrp` DECIMAL(10,2) NOT NULL,
    `sale_price` DECIMAL(10,2) NOT NULL,
    `stock_qty` DECIMAL(10,3) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    INDEX `idx_expiry` (`expiry_date`)
) ENGINE=InnoDB;

-- 3. Billing & Sales
CREATE TABLE IF NOT EXISTS `invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL, -- Cashier
    `customer_name` VARCHAR(100) NULL,
    `customer_phone` VARCHAR(20) NULL,
    `invoice_no` VARCHAR(50) NOT NULL UNIQUE,
    `sub_total` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `tax_total` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `discount_total` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `grand_total` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `payment_mode` ENUM('cash', 'card', 'upi', 'split') DEFAULT 'cash',
    `status` ENUM('paid', 'cancelled', 'refunded') DEFAULT 'paid',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    INDEX `idx_date` (`created_at`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `invoice_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `batch_id` INT NOT NULL,
    `qty` DECIMAL(10,3) NOT NULL,
    `unit_price` DECIMAL(10,2) NOT NULL, -- Price at moment of sale
    `tax_percent` DECIMAL(5,2) NOT NULL,
    `tax_amount` DECIMAL(10,2) NOT NULL,
    `total` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    FOREIGN KEY (`batch_id`) REFERENCES `product_batches`(`id`)
) ENGINE=InnoDB;

-- 4. Audit & Logging
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
