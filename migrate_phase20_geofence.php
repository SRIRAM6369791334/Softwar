<?php
/**
 * Migration for Phase 20: Geofenced Clock-In
 * Adds latitude, longitude, and radius to branches table
 */

require __DIR__ . '/app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

try {
    $pdo->beginTransaction();

    echo "Adding geofence columns to branches table...\n";
    
    // Check if columns exist
    $cols = $pdo->query("SHOW COLUMNS FROM branches LIKE 'latitude'")->fetchAll();
    if (count($cols) == 0) {
        $pdo->exec("
            ALTER TABLE `branches` 
            ADD COLUMN `latitude` DECIMAL(10, 8) NULL,
            ADD COLUMN `longitude` DECIMAL(11, 8) NULL,
            ADD COLUMN `geofence_radius` INT DEFAULT 100 -- in meters
        ");
        echo "Columns added: latitude, longitude, geofence_radius\n";
    } else {
        echo "Columns already exist.\n";
    }

    echo "Migration Phase 20 complete!\n";
    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Migration failed: " . $e->getMessage());
}
