<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Connection Manager
 * 
 * Singleton pattern ensures only one database connection per request.
 * All queries use PDO prepared statements to prevent SQL injection.
 * 
 * Usage:
 *   $db = Database::getInstance();
 *   $users = $db->query("SELECT * FROM users WHERE id = ?", [1])->fetchAll();
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        
        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false, // Real prepared statements
                ]
            );
        } catch (PDOException $e) {
            // In production, log this instead of displaying
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Execute a prepared statement query
     * 
     * @param string $sql SQL query with ? placeholders
     * @param array $params Parameters to bind (in order)
     * @return PDOStatement Executed statement (call fetch/fetchAll on this)
     * 
     * Example:
     *   $stmt = $db->query("SELECT * FROM products WHERE id = ?", [123]);
     *   $product = $stmt->fetch();
     */
    public function query(string $sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Get raw PDO connection (for transactions)
     * 
     * Example:
     *   $pdo = $db->getConnection();
     *   $pdo->beginTransaction();
     *   // ... multiple queries ...
     *   $pdo->commit();
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
