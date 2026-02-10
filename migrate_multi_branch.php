<?php

/**
 * Multi-Branch Migration Script
 * 
 * This script adds multi-branch support to the Supermarket OS.
 * It will:
 * 1. Create branches table
 * 2. Add branch_id columns to existing tables
 * 3. Create default "Main Branch"
 * 4. Assign all existing data to Main Branch
 * 
 * IMPORTANT: Backup your database before running this!
 */

echo "<h1>Multi-Branch Migration</h1>";
echo "<p>Starting migration...</p>";

try {
    // Load config
    $config = require __DIR__ . '/config/database.php';
    
    // Connect to database
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:blue'>✓ Connected to database: {$config['dbname']}</p>";
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Step 1: Create branches table
    echo "<h3>Step 1: Creating branches table...</h3>";
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
    echo "<p style='color:green'>✓ Branches table created</p>";
    
    // Step 2: Insert default "Main Branch"
    echo "<h3>Step 2: Creating default Main Branch...</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM branches");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("
            INSERT INTO branches (id, name, location, is_active) 
            VALUES (1, 'Main Branch', 'Headquarters', 1)
        ");
        echo "<p style='color:green'>✓ Main Branch created (ID: 1)</p>";
    } else {
        echo "<p style='color:orange'>⚠ Branches already exist, skipping default insert</p>";
    }
    
    // Step 3: Add branch_id to users table
    echo "<h3>Step 3: Updating users table...</h3>";
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'branch_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN branch_id INT NOT NULL DEFAULT 1 AFTER id");
        $pdo->exec("ALTER TABLE users ADD INDEX idx_branch_id (branch_id)");
        $pdo->exec("UPDATE users SET branch_id = 1 WHERE branch_id = 0");
        echo "<p style='color:green'>✓ Added branch_id to users table</p>";
    } else {
        echo "<p style='color:orange'>⚠ branch_id already exists in users</p>";
    }
    
    // Step 4: Add branch_id to products table
    echo "<h3>Step 4: Updating products table...</h3>";
    $result = $pdo->query("SHOW COLUMNS FROM products LIKE 'branch_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE products ADD COLUMN branch_id INT NOT NULL DEFAULT 1 AFTER id");
        $pdo->exec("ALTER TABLE products ADD INDEX idx_branch_id (branch_id)");
        $pdo->exec("UPDATE products SET branch_id = 1 WHERE branch_id = 0");
        echo "<p style='color:green'>✓ Added branch_id to products table</p>";
    } else {
        echo "<p style='color:orange'>⚠ branch_id already exists in products</p>";
    }
    
    // Step 5: Add branch_id to product_batches table
    echo "<h3>Step 5: Updating product_batches table...</h3>";
    $result = $pdo->query("SHOW COLUMNS FROM product_batches LIKE 'branch_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE product_batches ADD COLUMN branch_id INT NOT NULL DEFAULT 1 AFTER id");
        $pdo->exec("ALTER TABLE product_batches ADD INDEX idx_branch_id (branch_id)");
        $pdo->exec("UPDATE product_batches SET branch_id = 1 WHERE branch_id = 0");
        echo "<p style='color:green'>✓ Added branch_id to product_batches table</p>";
    } else {
        echo "<p style='color:orange'>⚠ branch_id already exists in product_batches</p>";
    }
    
    // Step 6: Add branch_id to invoices table
    echo "<h3>Step 6: Updating invoices table...</h3>";
    $result = $pdo->query("SHOW COLUMNS FROM invoices LIKE 'branch_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE invoices ADD COLUMN branch_id INT NOT NULL DEFAULT 1 AFTER id");
        $pdo->exec("ALTER TABLE invoices ADD INDEX idx_branch_id (branch_id)");
        $pdo->exec("UPDATE invoices SET branch_id = 1 WHERE branch_id = 0");
        echo "<p style='color:green'>✓ Added branch_id to invoices table</p>";
    } else {
        echo "<p style='color:orange'>⚠ branch_id already exists in invoices</p>";
    }
    
    // Step 7: Verify data counts
    echo "<h3>Step 7: Verifying migration...</h3>";
    $userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE branch_id = 1")->fetchColumn();
    $productCount = $pdo->query("SELECT COUNT(*) FROM products WHERE branch_id = 1")->fetchColumn();
    $batchCount = $pdo->query("SELECT COUNT(*) FROM product_batches WHERE branch_id = 1")->fetchColumn();
    $invoiceCount = $pdo->query("SELECT COUNT(*) FROM invoices WHERE branch_id = 1")->fetchColumn();
    
    echo "<ul>";
    echo "<li>Users in Main Branch: <strong>$userCount</strong></li>";
    echo "<li>Products in Main Branch: <strong>$productCount</strong></li>";
    echo "<li>Stock Batches in Main Branch: <strong>$batchCount</strong></li>";
    echo "<li>Invoices in Main Branch: <strong>$invoiceCount</strong></li>";
    echo "</ul>";
    
    // Commit transaction
    $pdo->commit();
    
    echo "<hr>";
    echo "<h2 style='color:green'>✅ Migration Completed Successfully!</h2>";
    echo "<p><strong>What's Next?</strong></p>";
    echo "<ul>";
    echo "<li>Go to Dashboard → Branches to add your other branches</li>";
    echo "<li>Use the branch selector to switch between branches</li>";
    echo "<li>All existing data is safely assigned to 'Main Branch'</li>";
    echo "</ul>";
    echo "<p><a href='/dashboard' style='color:#00f3ff; font-size:1.2rem;'>→ Go to Dashboard</a></p>";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<p style='color:red; font-size:1.2rem;'>✖ Migration Failed!</p>";
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Your database has not been modified. Please fix the error and try again.</p>";
}
