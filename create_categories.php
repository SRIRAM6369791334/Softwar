<?php
/**
 * Create categories table
 */

require __DIR__ . '/app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "Creating categories table...\n";
    
    // Create table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `categories` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `description` text DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✓ Categories table created\n\n";
    
    // Insert default categories
    echo "Inserting default categories...\n";
    $categories = [
        ['Groceries', 'General grocery items'],
        ['Beverages', 'Drinks and beverages'],
        ['Snacks', 'Snack foods'],
        ['Dairy', 'Milk, cheese, yogurt, etc.'],
        ['Bakery', 'Bread and baked goods'],
        ['Frozen Foods', 'Frozen items'],
        ['Personal Care', 'Toiletries and personal care products'],
        ['Household', 'Cleaning and household items']
    ];
    
    foreach ($categories as $cat) {
        $pdo->prepare("INSERT INTO `categories` (`name`, `description`) VALUES (?, ?)")
            ->execute($cat);
        echo "✓ Added: {$cat[0]}\n";
    }
    
    // Verify
    $count = $pdo->query("SELECT COUNT(*) as count FROM `categories`")->fetch();
    echo "\n✓ Total categories: {$count['count']}\n";
    echo "\nDone! /admin/data should now work!\n";
    
} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
