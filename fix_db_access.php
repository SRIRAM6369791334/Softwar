<?php
/**
 * Database Diagnostic & Fix Script
 */

require __DIR__ . '/app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "🔍 Database Diagnostic Tool\n";
    echo "========================\n\n";
    
    // 1. List all tables
    echo "📂 Checking Tables:\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
    echo "Found " . count($tables) . " tables: " . implode(', ', $tables) . "\n\n";
    
    $required = ['users', 'branches', 'categories', 'settings', 'workflows', 'workflow_actions'];
    $missing = array_diff($required, $tables);
    
    if (!empty($missing)) {
        echo "❌ Missing tables: " . implode(', ', $missing) . "\n";
        
        // Create Branches if missing
        if (in_array('branches', $missing)) {
             echo "🛠️ Creating 'branches' table...\n";
             $pdo->exec("
                CREATE TABLE `branches` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(100) NOT NULL,
                  `address` text DEFAULT NULL,
                  `phone` varchar(20) DEFAULT NULL,
                  `is_active` tinyint(1) NOT NULL DEFAULT 1,
                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
             ");
             
             echo "✅ Inserted default 'Main Branch'\n";
             $pdo->exec("INSERT INTO branches (name, address, is_active) VALUES ('Main Branch', 'HQ Address', 1)");
        }
        
    } else {
        echo "✅ All required tables exist.\n";
    }
    
    echo "\n";
    
    // 2. Check Users / Admin Access
    echo "👤 Checking Admin User:\n";
    $stmt = $pdo->query("SELECT * FROM users WHERE role_id = 1 OR username = 'admin' LIMIT 1");
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Admin user found: {$admin['username']} (ID: {$admin['id']})\n";
        
        // Reset password to 'admin123'
        $newPass = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newPass, $admin['id']]);
        echo "🔄 Password reset to: 'admin123'\n";
        
    } else {
        echo "❌ No admin user found!\n";
        echo "🛠️ Creating admin user...\n";
        
        $pass = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, role_id, email, branch_id) VALUES (?, ?, ?, ?, ?, ?)");
        // Try with branch_id 1 (Main Branch)
        try {
             $stmt->execute(['admin', $pass, 'System Administrator', 1, 'admin@example.com', 1]);
        } catch (\Exception $e) {
             // Maybe branch_id isn't needed or table structure differs? Try without branch_id
             $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, role_id, email) VALUES (?, ?, ?, ?, ?)");
             $stmt->execute(['admin', $pass, 'System Administrator', 1, 'admin@example.com']);
        }
            
        echo "✅ Admin user created: 'admin' / 'admin123'\n";
    }
    
    echo "\n========================\n";
    echo "Ready for testing!\n";
    
} catch (\Exception $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}
