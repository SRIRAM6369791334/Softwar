<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class BackupController extends Controller
{
    public function __construct()
    {
        // Strict Admin Check
        if (!Auth::check() || Auth::user()['role_id'] != 1) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
    }

    public function create()
    {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        // Get Tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
        
        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- User: " . Auth::user()['username'] . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Structure
            $row = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(\PDO::FETCH_NUM);
            $sql .= "\n\n" . $row[1] . ";\n\n";
            
            // Data
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $sql .= "INSERT INTO `$table` VALUES(";
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) $values[] = "NULL";
                    else $values[] = $pdo->quote($value);
                }
                $sql .= implode(", ", $values);
                $sql .= ");\n";
            }
        }
        
        $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

        // Download Response
        $filename = 'backup_' . date('Y-m-d_H-i') . '.sql';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sql));
        echo $sql;
        exit;
    }
}
