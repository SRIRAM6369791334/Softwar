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
                    PDO::ATTR_PERSISTENT => true, // Phase 8: Connection Persistence [#42]
                ]
            );
        } catch (PDOException $e) {
            // In production, log this instead of displaying
            die("Database connection failed: " . $e->getMessage());
        }
    }

    private array $queryCache = [];

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
     * @return \PDOStatement Executed statement (call fetch/fetchAll on this)
     */
    public function query(string $sql, array $params = [])
    {
        $start = microtime(true);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $executionTime = (microtime(true) - $start) * 1000; // ms

        // Phase 9: Performance Metrics [#64]
        if ($executionTime > 100) { // Log slow queries (> 100ms)
            try {
                (new ActivityMonitor())->logAdminAction(0, 'SLOW_QUERY', 'DATABASE', "Query took " . round($executionTime, 2) . "ms: " . substr($sql, 0, 100));
            } catch (\Throwable $e) {
                // Prevent circular dependency - silently fail if monitoring cannot be initialized
                error_log("Failed to log slow query: " . $e->getMessage());
            }
        }

        return $stmt;
    }

    /**
     * Execute a query and cache the result for the duration of the request
     * Useful for repeated dashboard lookups
     */
    public function cachedQuery(string $sql, array $params = [], int $ttl = 0)
    {
        $cacheKey = md5($sql . serialize($params));
        
        // Static Cache (Request Duration)
        if (isset($this->queryCache[$cacheKey])) {
            return $this->queryCache[$cacheKey];
        }

        $stmt = $this->query($sql, $params);
        $result = $stmt->fetchAll();
        
        $this->queryCache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Get raw PDO connection
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a callback within a database transaction
     * 
     * @param callable $callback function(Database $db)
     * @return mixed result of the callback
     * @throws \Exception
     */
    public function transactional(callable $callback)
    {
        $this->pdo->beginTransaction();
        try {
            $result = $callback($this);
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Get the last inserted ID
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
