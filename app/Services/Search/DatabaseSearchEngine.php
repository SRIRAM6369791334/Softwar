<?php

namespace App\Services\Search;

use App\Core\Database;

class DatabaseSearchEngine implements SearchEngineInterface
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(string $type, int $id, array $data): bool
    {
        // SQL doesn't need explicit indexing if we query live tables, 
        // but for high performance we might shadow it. 
        // For simplicity, we just return true.
        return true;
    }

    public function search(string $query, string $type = null): array
    {
        // Simple LIKE implementation for Products
        // A real implementation would map 'types' to tables dynamically.
        if ($type === 'products' || $type === null) {
            $sql = "SELECT id, name, sku, 'product' as type, 1.0 as score 
                    FROM products 
                    WHERE name LIKE ? OR sku LIKE ? 
                    LIMIT 20";
            return $this->db->query($sql, ["%$query%", "%$query%"])->fetchAll();
        }
        return [];
    }

    public function delete(string $type, int $id): bool
    {
        return true;
    }
}
