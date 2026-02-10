<?php
/**
 * Phase 27: Attendance Selfie Capture
 * Adds image + geo columns for clock-in / clock-out evidence.
 */

require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "=== Phase 27: Attendance Selfie Capture Migration ===\n\n";

try {
    $tables = $pdo->query("SHOW TABLES LIKE 'attendance_logs'")->fetchAll();
    if (count($tables) === 0) {
        throw new RuntimeException("attendance_logs table not found. Run migrate_phase19_employee.php first.");
    }

    $pdo->exec("
        ALTER TABLE attendance_logs
        ADD COLUMN IF NOT EXISTS clock_in_photo VARCHAR(255) NULL,
        ADD COLUMN IF NOT EXISTS clock_out_photo VARCHAR(255) NULL,
        ADD COLUMN IF NOT EXISTS clock_in_latitude DECIMAL(10, 8) NULL,
        ADD COLUMN IF NOT EXISTS clock_in_longitude DECIMAL(11, 8) NULL,
        ADD COLUMN IF NOT EXISTS clock_out_latitude DECIMAL(10, 8) NULL,
        ADD COLUMN IF NOT EXISTS clock_out_longitude DECIMAL(11, 8) NULL
    ");

    $uploadDir = __DIR__ . '/public/uploads/attendance';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    echo "✅ Phase 27 migration complete.\n";
    echo "Added columns: clock_in_photo, clock_out_photo, clock_in_latitude, clock_in_longitude, clock_out_latitude, clock_out_longitude\n";
    echo "Upload directory ready: public/uploads/attendance\n";
} catch (Throwable $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
