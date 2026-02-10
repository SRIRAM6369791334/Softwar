<?php

namespace App\Core;

/**
 * Backup Verifier
 * Ensures database backups are being created and are valid
 */
class BackupVerifier
{
    private $backupDir;
    
    public function __construct()
    {
        $this->backupDir = defined('APP_ROOT') ? APP_ROOT . '/storage/backups' : __DIR__ . '/../../storage/backups';
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    /**
     * Run verification on latest backup
     */
    public function verifyLatest(): array
    {
        $backups = glob($this->backupDir . '/*.sql*');
        
        if (empty($backups)) {
            return [
                'status' => 'failed',
                'message' => 'No backup files found in ' . $this->backupDir
            ];
        }
        
        // Sort by time
        usort($backups, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $latest = $backups[0];
        $size = filesize($latest);
        $age = time() - filemtime($latest);
        
        $results = [
            'file' => basename($latest),
            'size_bytes' => $size,
            'age_seconds' => $age,
            'status' => 'pass',
            'checks' => []
        ];
        
        // Check 1: Age (should be less than 25 hours if daily)
        if ($age > 90000) {
            $results['status'] = 'warning';
            $results['checks'][] = "Backup is old (" . round($age/3600, 1) . " hours)";
        }
        
        // Check 2: Size (minimum threshold, e.g., 5KB)
        if ($size < 5120) {
            $results['status'] = 'failed';
            $results['checks'][] = "Backup file is suspiciously small (" . round($size/1024, 2) . " KB)";
        }
        
        // Check 3: Content Integrity
        $isEncrypted = str_ends_with($latest, '.enc');
        $handle = fopen($latest, 'r');
        $header = fread($handle, 1024);
        fclose($handle);

        if ($header !== false) {
            if ($isEncrypted) {
                // For encrypted files, we just check if it has data. 
                // Deep validation would require the key.
                if (strlen($header) < 16) {
                    $results['status'] = 'failed';
                    $results['checks'][] = "Encrypted backup file is truncated or empty";
                }
            } else {
                if (!str_contains($header, 'CREATE TABLE') && !str_contains($header, 'INSERT INTO') && !str_contains($header, '-- MySQL dump')) {
                    $results['status'] = 'failed';
                    $results['checks'][] = "Backup file does not appear to be a valid SQL dump";
                }
            }
        }
        
        return $results;
    }
}
