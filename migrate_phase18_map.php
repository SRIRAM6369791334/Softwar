<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

echo "Migrating Map Tables...\n";

// 1. Map Sections
$db->query("CREATE TABLE IF NOT EXISTS `map_sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `grid_width` INT DEFAULT 10,
    `grid_height` INT DEFAULT 10,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;");
echo " - Created map_sections table\n";

// 2. Product Locations
$db->query("CREATE TABLE IF NOT EXISTS `product_locations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `section_id` INT NOT NULL,
    `x_coord` INT NOT NULL,
    `y_coord` INT NOT NULL,
    `z_layer` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`section_id`) REFERENCES `map_sections`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_pos` (`section_id`, `x_coord`, `y_coord`, `z_layer`)
) ENGINE=InnoDB;");
echo " - Created product_locations table\n";

echo "Migration Complete.\n";
