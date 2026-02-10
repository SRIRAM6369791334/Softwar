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

        // 2. System Health Check
        $this->checkSystemHealth($log);

        // 3. Daily Sales Alert (Simulated check for end-of-day)
        if (date('H:i') === '23:55' || isset($_GET['trigger_report'])) {
            $this->generateDailySummary($log);
        }

        // 4. Cleanup Old logs (e.g., older than 30 days)
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

    private function checkSystemHealth(&$log)
    {
        $monitor = new ActivityMonitor();
        $health = $monitor->checkSystemHealth();
        
        if ($health['status'] !== 'healthy') {
            foreach ($health['issues'] as $issue) {
                $log[] = "System Health Alert: " . $issue;
            }
        }
    }

    private function generateDailySummary(&$log)
    {
        $today = date('Y-m-d');
        $stats = $this->db->query(
            "SELECT COUNT(*) as count, SUM(grand_total) as total FROM invoices WHERE DATE(created_at) = ? AND status = 'paid'",
            [$today]
        )->fetch();
        
        Automation::trigger('daily_sales_summary', [
            'date' => $today,
            'invoice_count' => $stats['count'] ?? 0,
            'total_revenue' => $stats['total'] ?? 0,
            'branch' => 'All Branches'
        ]);

        // Daily Security/Anomaly Report
        (new ActivityMonitor())->generateDailyAnomalySummary();
        
        $log[] = "Generated Daily Sales & Security Summary for " . $today;
    }

    private function cleanupLogs(&$log)
    {
        // Phase 9: Data Retention Policy [#85]
        $retentionDays = $this->db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'retention_period_days'")->fetch()['setting_value'] ?? 730;
        
        $this->db->query("DELETE FROM automation_logs WHERE executed_at < DATE_SUB(NOW(), INTERVAL $retentionDays DAY)");
        $this->db->query("DELETE FROM action_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL $retentionDays DAY)");
        
        // Also cleanup processed background jobs older than 30 days
        $this->db->query("DELETE FROM background_jobs WHERE status IN ('completed', 'failed') AND updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");

        $log[] = "Cleaned up logs (Retention: $retentionDays days).";
    }
}
