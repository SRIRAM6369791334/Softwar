<?php
/**
 * Fix missing tables required by backtest scripts
 */
$c = require __DIR__ . '/config/database.php';
$pdo = new PDO("mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}", $c['username'], $c['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$fixes = [];

// 1. employee_attendance table (verify_employees.php needs it)
try {
    $pdo->query("SELECT 1 FROM employee_attendance LIMIT 1");
    echo "✔ employee_attendance already exists\n";
} catch (Exception $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `employee_attendance` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `clock_in` DATETIME NOT NULL,
        `clock_out` DATETIME NULL,
        `selfie_path` VARCHAR(255) NULL,
        `location_lat` DECIMAL(10,8) NULL,
        `location_lng` DECIMAL(11,8) NULL,
        `branch_id` INT NULL,
        `notes` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "✔ Created employee_attendance\n";
    $fixes[] = 'employee_attendance';
}

// 2. employee_roster table (verify_employees.php needs it)
try {
    $pdo->query("SELECT 1 FROM employee_roster LIMIT 1");
    echo "✔ employee_roster already exists\n";
} catch (Exception $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `employee_roster` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `branch_id` INT NOT NULL,
        `shift_date` DATE NOT NULL,
        `start_time` TIME NOT NULL,
        `end_time` TIME NOT NULL,
        `status` ENUM('scheduled','completed','absent') DEFAULT 'scheduled',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "✔ Created employee_roster\n";
    $fixes[] = 'employee_roster';
}

// 3. overtime_requests table (verify_employees.php needs it)
try {
    $pdo->query("SELECT 1 FROM overtime_requests LIMIT 1");
    echo "✔ overtime_requests already exists\n";
} catch (Exception $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `overtime_requests` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `date` DATE NOT NULL,
        `hours` DECIMAL(4,2) NOT NULL,
        `reason` TEXT NULL,
        `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
        `approved_by` INT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "✔ Created overtime_requests\n";
    $fixes[] = 'overtime_requests';
}

// 4. stock_transfers table - check if it has from_branch column (verify_stock_flow.php needs it)
try {
    $pdo->query("SELECT from_branch FROM stock_transfers LIMIT 1");
    echo "✔ stock_transfers.from_branch exists\n";
} catch (Exception $e) {
    // Table might not exist, or column might be named differently
    try {
        $pdo->query("SELECT 1 FROM stock_transfers LIMIT 1");
        // Table exists but column missing - check actual columns
        $cols = $pdo->query("SHOW COLUMNS FROM stock_transfers")->fetchAll(PDO::FETCH_COLUMN);
        echo "  stock_transfers columns: " . implode(', ', $cols) . "\n";
        
        // Check if it uses source_branch instead
        if (in_array('source_branch_id', $cols) || in_array('source_branch', $cols)) {
            echo "  Has different column naming, recreating...\n";
        }
        
        // Add from_branch if missing
        if (!in_array('from_branch', $cols)) {
            try {
                $pdo->exec("ALTER TABLE stock_transfers ADD COLUMN `from_branch` INT NULL AFTER `id`");
                echo "✔ Added from_branch column\n";
            } catch (Exception $e2) {
                echo "  Could not add from_branch: " . $e2->getMessage() . "\n";
            }
        }
        if (!in_array('to_branch', $cols)) {
            try {
                $pdo->exec("ALTER TABLE stock_transfers ADD COLUMN `to_branch` INT NULL");
                echo "✔ Added to_branch column\n";
            } catch (Exception $e2) {
                echo "  Could not add to_branch: " . $e2->getMessage() . "\n";
            }
        }
    } catch (Exception $e2) {
        // Table does not exist at all
        $pdo->exec("CREATE TABLE IF NOT EXISTS `stock_transfers` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `from_branch` INT NOT NULL,
            `to_branch` INT NOT NULL,
            `status` ENUM('pending','in_transit','received','cancelled') DEFAULT 'pending',
            `created_by` INT NOT NULL,
            `notes` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");
        echo "✔ Created stock_transfers table\n";
        $fixes[] = 'stock_transfers';
    }
}

// 5. purchase_orders table (verify_stock_flow.php needs it)
try {
    $pdo->query("SELECT 1 FROM purchase_orders LIMIT 1");
    echo "✔ purchase_orders already exists\n";
} catch (Exception $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `purchase_orders` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `vendor_id` INT NOT NULL,
        `branch_id` INT NOT NULL,
        `order_no` VARCHAR(50) NOT NULL,
        `total_amount` DECIMAL(12,2) DEFAULT 0,
        `status` ENUM('draft','ordered','received','cancelled') DEFAULT 'draft',
        `created_by` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "✔ Created purchase_orders\n";
    $fixes[] = 'purchase_orders';
}

// 6. vendors table (verify_stock_flow.php needs it)
try {
    $pdo->query("SELECT 1 FROM vendors LIMIT 1");
    echo "✔ vendors already exists\n";
} catch (Exception $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `vendors` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `contact_person` VARCHAR(100) NULL,
        `phone` VARCHAR(20) NULL,
        `email` VARCHAR(100) NULL,
        `address` TEXT NULL,
        `gst_no` VARCHAR(20) NULL,
        `status` ENUM('active','inactive') DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    // Insert a test vendor
    $pdo->exec("INSERT INTO vendors (name, contact_person, phone) VALUES ('Test Supplier', 'John', '9876543210')");
    echo "✔ Created vendors + test vendor\n";
    $fixes[] = 'vendors';
}

// 7. maintenance_logs table (verify_maintenance.php needs it)
try {
    $pdo->query("SELECT 1 FROM maintenance_logs LIMIT 1");
    echo "✔ maintenance_logs already exists\n";
} catch (Exception $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `maintenance_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `equipment_name` VARCHAR(150) NOT NULL,
        `issue` TEXT NOT NULL,
        `status` ENUM('reported','in_progress','resolved') DEFAULT 'reported',
        `reported_by` INT NOT NULL,
        `branch_id` INT NULL,
        `resolved_at` DATETIME NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "✔ Created maintenance_logs\n";
    $fixes[] = 'maintenance_logs';
}

echo "\n=== Summary ===\n";
if (empty($fixes)) {
    echo "No fixes needed. All tables exist.\n";
} else {
    echo "Fixed " . count($fixes) . " table(s): " . implode(', ', $fixes) . "\n";
}
