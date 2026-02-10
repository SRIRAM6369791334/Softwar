<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║  PHASE 3: BUSINESS LOGIC HARDENING MIGRATION           ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

$sql = [
    "ALTER TABLE invoices ADD COLUMN discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER tax_total",
    
    "CREATE TABLE IF NOT EXISTS refund_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_id INT NOT NULL,
        user_id INT NOT NULL COMMENT 'Cashier who requested',
        reason TEXT,
        amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        approved_by INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_invoice (invoice_id),
        INDEX idx_status (status),
        FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "ALTER TABLE users ADD COLUMN max_discount_percent DECIMAL(5,2) NOT NULL DEFAULT 10.00",
    "ALTER TABLE product_batches ADD COLUMN is_expired_blocked TINYINT(1) NOT NULL DEFAULT 1"
];

foreach ($sql as $stmt) {
    try {
        $pdo->exec($stmt);
        echo "✅ Success: " . substr($stmt, 0, 50) . "...\n";
    } catch (\PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column')) {
            echo "⚠️  Already exists: " . substr($stmt, 0, 50) . "...\n";
        } else {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }
}
