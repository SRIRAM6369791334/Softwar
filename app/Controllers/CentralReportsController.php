<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class CentralReportsController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole(1); // Admin Only
    }

    /**
     * Cross-branch aggregated dashboard
     */
    public function dashboard()
    {
        // 1. Total Aggregated Stats
        $today = date('Y-m-d');
        $aggStats = $this->db->query("
            SELECT 
                COUNT(DISTINCT branch_id) as total_active_branches,
                SUM(grand_total) as total_today_sales,
                COUNT(id) as total_today_bills
            FROM invoices 
            WHERE DATE(created_at) = ? AND status = 'paid'
        ", [$today])->fetch();

        // 2. Month-to-Date Aggregated Sales
        $monthStart = date('Y-m-01');
        $mtdSales = $this->db->query("
            SELECT SUM(grand_total) as amt 
            FROM invoices 
            WHERE created_at >= ? AND status = 'paid'
        ", [$monthStart])->fetch();

        // 3. Per Branch Performance Today
        $branchPerformance = $this->db->query("
            SELECT 
                b.name as branch_name,
                COUNT(i.id) as bills_count,
                COALESCE(SUM(i.grand_total), 0) as sales_total
            FROM branches b
            LEFT JOIN invoices i ON b.id = i.branch_id AND DATE(i.created_at) = ? AND i.status = 'paid'
            WHERE b.is_active = 1
            GROUP BY b.id
            ORDER BY sales_total DESC
        ", [$today])->fetchAll();

        // 4. Inventory Health (Aggregated)
        $lowStockCount = $this->db->query("
            SELECT COUNT(*) as count 
            FROM products p
            WHERE (
                SELECT COALESCE(SUM(stock_qty), 0) 
                FROM product_batches 
                WHERE product_id = p.id
            ) < p.min_stock_alert
        ")->fetch();

        return $this->view('central/dashboard', [
            'agg_stats' => $aggStats,
            'mtd_sales' => $mtdSales['amt'] ?? 0,
            'branch_performance' => $branchPerformance,
            'low_stock_total' => $lowStockCount['count'] ?? 0
        ], 'dashboard');
    }

    /**
     * Branch Comparison Analytics
     */
    public function comparison()
    {
        // For comparison, we'll look at the last 7 days of sales per branch
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = date('Y-m-d', strtotime("-$i days"));
        }

        $branches = $this->db->query("SELECT id, name FROM branches WHERE is_active = 1")->fetchAll();
        $comparisonData = [];

        foreach ($branches as $branch) {
            $dailySales = [];
            foreach ($labels as $date) {
                $sale = $this->db->query("
                    SELECT SUM(grand_total) as amt 
                    FROM invoices 
                    WHERE branch_id = ? AND DATE(created_at) = ? AND status = 'paid'
                ", [$branch['id'], $date])->fetch();
                $dailySales[] = $sale['amt'] ?? 0;
            }
            $comparisonData[] = [
                'name' => $branch['name'],
                'data' => $dailySales
            ];
        }

        return $this->view('central/comparison', [
            'labels' => $labels,
            'comparison_data' => $comparisonData
        ], 'dashboard');
    }
}
