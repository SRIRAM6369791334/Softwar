<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting Phase 25 Migration: Automation Engines...\n";

try {
    // 1. Workflows Table (The "Rule")
    echo "Creating 'workflows' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS workflows (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        trigger_event VARCHAR(50) NOT NULL, -- e.g., 'user_created', 'low_stock', 'sale_completed'
        description TEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);

    // 2. Workflow Actions Table (The "Effect")
    echo "Creating 'workflow_actions' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS workflow_actions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        workflow_id INT NOT NULL,
        action_type VARCHAR(50) NOT NULL, -- e.g., 'send_email', 'create_notification', 'http_request'
        action_payload JSON, -- Configuration for the action (e.g., template_id, recipient, url)
        sort_order INT DEFAULT 0,
        FOREIGN KEY (workflow_id) REFERENCES workflows(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);

    // 3. Automation Logs (History)
    echo "Creating 'automation_logs' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS automation_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        workflow_id INT,
        trigger_context JSON, -- Data that triggered it
        status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
        message TEXT,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);

    // 4. Seed Default Workflow (Welcome Email)
    // We already hardcoded this in UserController, but let's migrate it provided it doesn't exist
    $check = $pdo->query("SELECT id FROM workflows WHERE trigger_event = 'user_created'")->fetch();
    if (!$check) {
        echo "Seeding default 'New Employee Welcome' workflow...\n";
        $pdo->exec("INSERT INTO workflows (name, trigger_event, description) VALUES ('New Employee Welcome', 'user_created', 'Send welcome email when new staff is added')");
        $wfId = $pdo->lastInsertId();
        
        $payload = json_encode([
            'template_key' => 'email_welcome', 
            'recipient_field' => 'email'
        ]);
        
        $pdo->exec("INSERT INTO workflow_actions (workflow_id, action_type, action_payload) VALUES ($wfId, 'send_email', '$payload')");
    }

    echo "Phase 25 Migration Complete!\n";

} catch (PDOException $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}
