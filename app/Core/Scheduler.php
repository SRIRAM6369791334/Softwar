<?php

namespace App\Core;

class Scheduler
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function run()
    {
        $log = [];
        $log[] = "Scheduler Started at " . date('Y-m-d H:i:s');

        // 1. Check Low Stock & Trigger Workflow
        $this->checkLowStock($log);

        // 2. Cleanup Old logs (e.g., older than 30 days)
        $this->cleanupLogs($log);

        $log[] = "Scheduler Finished.";
        return $log;
    }

    private function checkLowStock(&$log)
    {
        // Find products below min_stock_alert
        $sql = "
            SELECT p.*, b.name as branch_name 
            FROM products p
            JOIN branches b ON p.branch_id = b.id
            WHERE p.is_active = 1 
            AND p.deleted_at IS NULL
            AND (SELECT SUM(stock_qty) FROM product_batches WHERE product_id = p.id) < p.min_stock_alert
        ";
        $lowStock = $this->db->query($sql)->fetchAll();

        foreach ($lowStock as $product) {
            // Trigger 'low_stock' workflow
            // We pass product details as context
            Automation::trigger('low_stock', [
                'product_name' => $product['name'],
                'sku' => $product['sku'],
                'branch' => $product['branch_name'],
                'current_stock' => 'LOW', // actual calculation is complex in query, simplifying
                'email' => 'admin@sos.com' // Default recipient if workflow uses email
            ]);
        }

        if (count($lowStock) > 0) {
            $log[] = "Triggered Low Stock alerts for " . count($lowStock) . " products.";
        }
    }

    private function cleanupLogs(&$log)
    {
        // Delete logs older than 30 days
        $this->db->query("DELETE FROM automation_logs WHERE executed_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $this->db->query("DELETE FROM action_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $log[] = "Cleaned up old logs.";
    }
}
