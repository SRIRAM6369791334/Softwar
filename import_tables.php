<?php
/**
 * Import SQL script to create missing tables
 */

require __DIR__ . '/app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getConnection();
    
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/missing_tables.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "Importing missing tables...\n\n";
    
    $success = 0;
    $failed = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || str_starts_with($statement, '--')) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $success++;
            
            // Show what was executed (first 60 chars)
            $preview = substr($statement, 0, 60);
            echo "✓ " . $preview . (strlen($statement) > 60 ? '...' : '') . "\n";
        } catch (\PDOException $e) {
            $failed++;
            $preview = substr($statement, 0, 60);
            echo "✗ " . $preview . (strlen($statement) > 60 ? '...' : '') . "\n";
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n========================================\n";
    echo "Import Complete!\n";
    echo "Success: $success statements\n";
    echo "Failed: $failed statements\n";
    echo "========================================\n";
    
    // Verify tables were created
    echo "\nVerifying tables...\n";
    $tables = ['categories', 'settings', 'workflows', 'workflow_actions'];
    
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT COUNT(*) as count FROM `$table`")->fetch();
            echo "✓ Table '$table' exists with {$result['count']} rows\n";
        } catch (\PDOException $e) {
            echo "✗ Table '$table' does not exist\n";
        }
    }
    
} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

echo "\nDone! You can now access /admin/data and /admin/settings\n";
