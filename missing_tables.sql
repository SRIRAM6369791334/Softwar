-- Missing Tables for Supermarket OS
-- These tables are required by the admin controllers

-- Categories table for product categorization
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table for system configuration
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL,
  `input_type` varchar(20) NOT NULL DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Workflows table for automation
CREATE TABLE IF NOT EXISTS `workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `trigger_event` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Workflow Actions table
CREATE TABLE IF NOT EXISTS `workflow_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`action_payload`)),
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`),
  CONSTRAINT `workflow_actions_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some default categories
INSERT INTO `categories` (`name`, `description`) VALUES
('Groceries', 'General grocery items'),
('Beverages', 'Drinks and beverages'),
('Snacks', 'Snack foods'),
('Dairy', 'Milk, cheese, yogurt, etc.'),
('Bakery', 'Bread and baked goods'),
('Frozen Foods', 'Frozen items'),
('Personal Care', 'Toiletries and personal care products'),
('Household', 'Cleaning and household items')
ON DUPLICATE KEY UPDATE name=name;

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `input_type`, `description`) VALUES
('store_name', 'Supermarket OS', 'general', 'text', 'Name of the store'),
('store_currency', 'USD', 'general', 'text', 'Currency code'),
('tax_rate', '0', 'general', 'number', 'Default tax rate percentage'),
('enable_notifications', '1', 'general', 'boolean', 'Enable system notifications'),
('backup_enabled', '1', 'security', 'boolean', 'Enable automatic backups')
ON DUPLICATE KEY UPDATE setting_key=setting_key;
