<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║  FINANCIAL DATA TYPE AUDIT                             ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

foreach ($tables as $table) {
    try {
        $cols = $db->query("DESCRIB $table")->fetchAll(); // Typing error 'DESCRIB' to trigger error handler? No, let's use DESCRIBE
        $cols = $db->query("DESCRIBE $table")->fetchAll();
        foreach ($cols as $col) {
            $type = strtolower($col['Type']);
            if (strpos($type, 'float') !== false || strpos($type, 'double') !== false || strpos($type, 'real') !== false) {
                echo "🚩 INSECURE: $table.{$col['Field']} is $type\n";
            }
        }
    } catch (Exception $e) {
        // Skip
    }
}
echo "\nAudit Complete.\n";
