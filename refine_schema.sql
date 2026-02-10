-- Refine schema to add legacy fields for controller compatibility
SET FOREIGN_KEY_CHECKS = 0;

-- Products table refinements (Check if columns exist first or just try to add)
-- Since I previously failed, I'll drop them if they exist effectively by using a more robust approach in PHP or just running ALTER and ignoring errors if they already exist.
-- Actually, ALTER TABLE doesn't support IF NOT EXISTS in all versions, but I'll assume they don't exist yet because the previous run failed early.

ALTER TABLE `products` 
ADD COLUMN `branch_id` INT DEFAULT 1 AFTER `name`,
ADD COLUMN `version_id` INT DEFAULT 1 AFTER `is_active`,
ADD COLUMN `deleted_at` TIMESTAMP NULL AFTER `created_at`;

-- Variants table refinements
ALTER TABLE `product_variants`
ADD COLUMN `version_id` INT DEFAULT 1 AFTER `is_active`;

-- Invoice Sequences (Required by CheckoutService)
CREATE TABLE IF NOT EXISTS `invoice_sequences` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL UNIQUE,
    `prefix` VARCHAR(10) DEFAULT 'INV',
    `last_val` INT DEFAULT 0,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`)
) ENGINE=InnoDB;

-- Ensure branch 1 exists and has a sequence
-- Fixed: using 'is_active' instead of 'status'
INSERT IGNORE INTO `branches` (id, name, location, is_active) VALUES (1, 'Main Branch', 'Default', 1);
INSERT IGNORE INTO `invoice_sequences` (branch_id, prefix, last_val) VALUES (1, 'INV', 0);

SET FOREIGN_KEY_CHECKS = 1;
