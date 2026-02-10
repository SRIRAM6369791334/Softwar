-- Supermarket OS Master Schema (Standardized)
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables that will be rebuilt
DROP TABLE IF EXISTS `product_variants`;
DROP TABLE IF EXISTS `product_batches`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `subbrands`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `subcategories`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `tax_groups`;
DROP TABLE IF EXISTS `tax_slabs`;
DROP TABLE IF EXISTS `units`;
DROP TABLE IF EXISTS `storage_types`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `invoice_sequences`;

-- 1. Categories
CREATE TABLE `categories` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Subcategories
CREATE TABLE `subcategories` (
    `id` INT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Brands
CREATE TABLE `brands` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Subbrands
CREATE TABLE `subbrands` (
    `id` INT PRIMARY KEY,
    `brand_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Units
CREATE TABLE `units` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 6. Storage Types
CREATE TABLE `storage_types` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. Tax Groups (renamed from tax_slabs for compatibility)
CREATE TABLE `tax_groups` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `percentage` DECIMAL(5,2) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 8. Suppliers
CREATE TABLE `suppliers` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `contact_person` VARCHAR(100),
    `phone` VARCHAR(20),
    `email` VARCHAR(100),
    `gst_number` VARCHAR(20),
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 9. Products
CREATE TABLE `products` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `branch_id` INT DEFAULT 1,
    `category_id` INT,
    `subcategory_id` INT,
    `brand_id` INT,
    `subbrand_id` INT,
    `hsn_code` VARCHAR(20),
    `storage_type_id` INT,
    `is_active` TINYINT(1) DEFAULT 1,
    `version_id` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`subbrand_id`) REFERENCES `subbrands`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`storage_type_id`) REFERENCES `storage_types`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 10. Product Variants
CREATE TABLE `product_variants` (
    `id` INT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `variant_name` VARCHAR(255),
    `barcode` VARCHAR(50) UNIQUE,
    `sku_code` VARCHAR(50) UNIQUE,
    `unit_id` INT,
    `mrp` DECIMAL(10,2) NOT NULL,
    `selling_price` DECIMAL(10,2) NOT NULL,
    `purchase_price` DECIMAL(10,2) NOT NULL,
    `tax_slab_id` INT, -- keeping internal name for logic separation but it links to tax_groups.id
    `min_stock_level` INT DEFAULT 10,
    `max_stock_level` INT DEFAULT 100,
    `reorder_level` INT DEFAULT 20,
    `current_stock` DECIMAL(10,2) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `version_id` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`unit_id`) REFERENCES `units`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`tax_slab_id`) REFERENCES `tax_groups`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 11. Invoice Sequences
CREATE TABLE IF NOT EXISTS `invoice_sequences` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL UNIQUE,
    `prefix` VARCHAR(10) DEFAULT 'INV',
    `last_val` INT DEFAULT 0,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`)
) ENGINE=InnoDB;

-- Ensure branch 1 exists
INSERT IGNORE INTO `branches` (id, name, location, is_active) VALUES (1, 'Main Branch', 'Default', 1);
INSERT IGNORE INTO `invoice_sequences` (branch_id, prefix, last_val) VALUES (1, 'INV', 0);

SET FOREIGN_KEY_CHECKS = 1;
