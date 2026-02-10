<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

echo "Migrating Biometric Tables...\n";

// 1. User Biometrics
$db->query("CREATE TABLE IF NOT EXISTS `user_biometrics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `credential_id` TEXT NOT NULL,
    `public_key` TEXT NULL,
    `label` VARCHAR(100) DEFAULT 'My Device',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;");
echo " - Created user_biometrics table\n";

echo "Migration Complete.\n";
