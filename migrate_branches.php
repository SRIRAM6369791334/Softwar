<?php
// Simple migration runner that outputs plain text
require __DIR__ . '/config/database.php';

$config = require __DIR__ . '/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database\n";
    
    $pdo->beginTransaction();
    
    // Create branches table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS branches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            location VARCHAR(200),
            manager_id INT,
            phone VARCHAR(15),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Branches table created\n";
    
    // Insert default branch
    $stmt = $pdo->query("SELECT COUNT(*) FROM branches");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO branches (id, name, location, is_active) VALUES (1, 'Main Branch', 'Headquarters', 1)");
        echo "✓ Main Branch created\n";
    }
    
    // Add branch_id to users
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'branch_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN branch_id INT NOT NULL DEFAULT 1 AFTER id");
        $pdo->exec("ALTER TABLE users ADD INDEX idx_branch_id (branch_id)");
        echo "✓ Added branch_id to users\n";
    }
    
    // Add branch_id to products
    $result = $pdo->query("SHOW COLUMNS FROM products LIKE 'branch_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE products ADD COLUMN branch_id INT NOT NULL DEFAULT 1 AFTER id");
        $pdo->exec("ALTER TABLE products ADD INDEX idx_branch_id (branch_id)");
        echo "✓ Added branch_id to products\n";
    }
    
    // Add branch_id to product_batches
    $result = $pdo->query("SHOW COLUMNS FROM product_batches LIKE 'branch_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE product_batches ADD COLUMN branch_id INT NOT NULL DEFAULT 1 AFTER id");
        $pdo->exec("ALTER TABLE product_batches ADD INDEX idx_branch_id (branch_id)");
        echo "✓ Added branch_id to product_batches\n";
    }
    
    // Add branch_id to invoices
    $result = $pdo->query("SHOW COLUMNS FROM invoices LIKE 'branch_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN branch_id INT NOT NULL DEFAULT 1 AFTER id");
        $pdo->exec("ALTER TABLE invoices ADD INDEX idx_branch_id (branch_id)");
        echo "✓ Added branch_id to invoices\n";
    }
    
    // Only commit if we're in a transaction
    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "You can now access /branches to manage your branches.\n";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "✖ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
