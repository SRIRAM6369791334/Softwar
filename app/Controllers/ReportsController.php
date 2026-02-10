<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class ReportsController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function daybook()
    {
        // Get date filter or default to today (filtered by current branch)
        $date = $_GET['date'] ?? date('Y-m-d');
        $branchId = Auth::getCurrentBranch();
        
        $sql = "
            SELECT i.*, u.full_name as cashier_name
            FROM invoices i
            JOIN users u ON i.user_id = u.id
            WHERE DATE(i.created_at) = ? AND i.branch_id = ?
            ORDER BY i.created_at DESC
        ";
        
        $invoices = $this->db->query($sql, [$date, $branchId])->fetchAll();
        
        // Calculate day totals (filtered by current branch)
        $totals = $this->db->query("
            SELECT 
                COUNT(*) as total_bills,
                SUM(grand_total) as total_sales,
                SUM(tax_total) as total_tax,
                SUM(discount_total) as total_discount
            FROM invoices 
            WHERE DATE(created_at) = ? AND status = 'paid' AND branch_id = ?
        ", [$date, $branchId])->fetch();
        
        return $this->view('reports/daybook', [
            'invoices' => $invoices,
            'totals' => $totals,
            'date' => $date
        ], 'dashboard');
    }

    public function invoice($id)
    {
        // Get invoice header
        $invoice = $this->db->query("
            SELECT i.*, u.full_name as cashier_name
            FROM invoices i
            JOIN users u ON i.user_id = u.id
            WHERE i.id = ?
        ", [$id])->fetch();

        if (!$invoice) {
            die("Invoice not found");
        }

        // Get line items
        $items = $this->db->query("
            SELECT ii.*, p.name as product_name, p.sku, pb.batch_no
            FROM invoice_items ii
            JOIN products p ON ii.product_id = p.id
            JOIN product_batches pb ON ii.batch_id = pb.id
            WHERE ii.invoice_id = ?
        ", [$id])->fetchAll();

        return $this->view('reports/invoice', [
            'invoice' => $invoice,
            'items' => $items
        ], 'dashboard');
    }

    public function dashboard()
    {
        // Phase 16: Check for new alerts on dashboard load
        \App\Controllers\NotificationController::checkAlerts();

        // Quick Summary Stats (filtered by current branch)
        $branchId = Auth::getCurrentBranch();
        $today = date('Y-m-d');
        $todaySales = $this->db->query(
            "SELECT SUM(grand_total) as amt FROM invoices WHERE DATE(created_at) = ? AND status='paid' AND branch_id = ?", 
            [$today, $branchId]
        )->fetch();
        
        $monthStart = date('Y-m-01');
        $monthSales = $this->db->query(
            "SELECT SUM(grand_total) as amt FROM invoices WHERE created_at >= ? AND status='paid' AND branch_id = ?", 
            [$monthStart, $branchId]
        )->fetch();
        
        // Optimized Low Stock Query (Using Join instead of Correlated Subquery)
        $lowStock = $this->db->query("
            SELECT COUNT(*) as count FROM (
                SELECT p.id
                FROM products p
                LEFT JOIN product_batches pb ON p.id = pb.product_id AND pb.branch_id = ?
                WHERE p.branch_id = ? AND p.is_active = 1 AND p.deleted_at IS NULL
                GROUP BY p.id, p.min_stock_alert
                HAVING COALESCE(SUM(pb.stock_qty), 0) < p.min_stock_alert
            ) as low_stock_list
        ", [$branchId, $branchId])->fetch();

        return $this->view('reports/dashboard', [
            'today_sales' => $todaySales['amt'] ?? 0,
            'month_sales' => $monthSales['amt'] ?? 0,
            'low_stock_items' => $lowStock['count'] ?? 0
        ], 'dashboard');
    }

    public function topProducts()
    {
        $this->requireRole([1, 2]); // Admin & Manager
        // Get date range from query params (filtered by current branch)
        $branchId = Auth::getCurrentBranch();
        $startDate = $_GET['start'] ?? date('Y-m-01'); // Default to month start
        $endDate = $_GET['end'] ?? date('Y-m-d');

        // Top Selling Products by Revenue (Cached for request duration)
        $topSellers = $this->db->cachedQuery("
            SELECT 
                p.name, 
                p.sku,
                SUM(ii.qty) as total_qty,
                SUM(ii.total) as total_revenue
            FROM invoice_items ii
            JOIN products p ON ii.product_id = p.id
            JOIN invoices i ON ii.invoice_id = i.id
            WHERE DATE(i.created_at) BETWEEN ? AND ? AND i.status = 'paid' AND i.branch_id = ?
            GROUP BY p.id
            ORDER BY total_revenue DESC
            LIMIT 20
        ", [$startDate, $endDate, $branchId]);

        // Slow Moving / Dead Stock (within current branch)
        $slowMovers = $this->db->query("
            SELECT 
                p.name, 
                p.sku,
                COALESCE(SUM(pb.stock_qty), 0) as current_stock,
                COALESCE(sales.total_qty, 0) as qty_sold
            FROM products p
            LEFT JOIN product_batches pb ON p.id = pb.product_id AND pb.branch_id = ?
            LEFT JOIN (
                SELECT product_id, SUM(qty) as total_qty
                FROM invoice_items ii
                JOIN invoices i ON ii.invoice_id = i.id
                WHERE DATE(i.created_at) BETWEEN ? AND ? AND i.branch_id = ?
                GROUP BY product_id
            ) sales ON p.id = sales.product_id
            WHERE p.branch_id = ?
            GROUP BY p.id
            HAVING current_stock > 0 AND qty_sold < 5
            ORDER BY qty_sold ASC
            LIMIT 20
        ", [$branchId, $startDate, $endDate, $branchId, $branchId])->fetchAll();

        return $this->view('reports/top_products', [
            'topSellers' => $topSellers,
            'slowMovers' => $slowMovers,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'dashboard');
    }

    public function employeePerformance()
    {
        $this->requireRole([1, 2]); // Admin & Manager
        $startDate = $_GET['start'] ?? date('Y-m-01');
        $endDate = $_GET['end'] ?? date('Y-m-d');
        $branchId = Auth::getCurrentBranch();

        $performance = $this->db->query("
            SELECT 
                u.full_name,
                u.username,
                COUNT(i.id) as total_bills,
                SUM(i.grand_total) as total_sales,
                AVG(i.grand_total) as avg_bill_value
            FROM users u
            LEFT JOIN invoices i ON u.id = i.user_id 
                AND DATE(i.created_at) BETWEEN ? AND ? 
                AND i.status = 'paid'
                AND i.branch_id = ?
            WHERE u.role_id IN (1, 2, 3) AND u.branch_id = ?
            GROUP BY u.id
            ORDER BY total_sales DESC
        ", [$startDate, $endDate, $branchId, $branchId])->fetchAll();

        return $this->view('reports/employee_performance', [
            'performance' => $performance,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'dashboard');
    }
    public function reorderReport()
    {
        $this->requireRole([1, 2]); // Admin & Manager
        $branchId = Auth::getCurrentBranch();
        
        $sql = "
            SELECT p.id, p.name, p.sku, p.unit,
                   COALESCE(bps.min_stock_alert, p.min_stock_alert) as alert_level,
                   COALESCE(SUM(pb.stock_qty), 0) as current_stock
            FROM products p
            LEFT JOIN branch_product_settings bps ON p.id = bps.product_id AND bps.branch_id = ?
            LEFT JOIN product_batches pb ON p.id = pb.product_id AND pb.branch_id = ?
            WHERE p.is_active = 1 AND p.branch_id = ?
            GROUP BY p.id
            HAVING SUM(COALESCE(pb.stock_qty, 0)) < alert_level
            ORDER BY (SUM(COALESCE(pb.stock_qty, 0)) / alert_level) ASC
        ";
        
        $reorderList = $this->db->query($sql, [$branchId, $branchId, $branchId])->fetchAll();
        
        return $this->view('reports/reorder_report', [
            'reorder_list' => $reorderList
        ], 'dashboard');
    }

    /**
     * Phase 14: GST Compliance Features
     */
    public function gstDashboard()
    {
        $this->requireRole(1); // Admin Only
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $branch_id = $_SESSION['branch_id'] ?? 1;

        // B2C Small Invoices - Aggregated by Tax Rate
        $b2c = $this->db->query("
            SELECT 
                ii.tax_percent,
                SUM(ii.qty * ii.unit_price) as taxable_value,
                SUM(ii.tax_amount) as total_tax
            FROM invoice_items ii
            JOIN invoices i ON ii.invoice_id = i.id
            WHERE i.branch_id = ? 
              AND i.status = 'paid' 
              AND MONTH(i.created_at) = ? 
              AND YEAR(i.created_at) = ?
            GROUP BY ii.tax_percent
        ", [$branch_id, $month, $year])->fetchAll();

        // Calculate Totals
        $totals = ['taxable' => 0, 'tax' => 0];
        foreach($b2c as $row) {
            $totals['taxable'] += $row['taxable_value'];
            $totals['tax'] += $row['total_tax'];
        }

        return $this->view('reports/gst/dashboard', [
            'month' => $month,
            'year' => $year,
            'b2c' => $b2c,
            'totals' => $totals
        ], 'dashboard');
    }

    public function hsnSummary()
    {
        $this->requireRole(1); // Admin Only
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $branch_id = $_SESSION['branch_id'] ?? 1;

        $hsnData = $this->db->query("
            SELECT 
                COALESCE(p.hsn_code, 'N/A') as hsn_code,
                p.unit,
                SUM(ii.qty) as total_qty,
                SUM(ii.total) as total_value,
                SUM(ii.qty * ii.unit_price) as taxable_value,
                SUM(ii.tax_amount) as total_tax,
                ii.tax_percent
            FROM invoice_items ii
            JOIN products p ON ii.product_id = p.id
            JOIN invoices i ON ii.invoice_id = i.id
            WHERE i.branch_id = ? 
              AND i.status = 'paid' 
              AND MONTH(i.created_at) = ? 
              AND YEAR(i.created_at) = ?
            GROUP BY p.hsn_code, ii.tax_percent, p.unit
        ", [$branch_id, $month, $year])->fetchAll();

        return $this->view('reports/gst/hsn_summary', [
            'month' => $month,
            'year' => $year,
            'hsnData' => $hsnData
        ], 'dashboard');
    }

    public function exportGstCsv()
    {
        $this->requireRole(1); // Admin Only
        $type = $_GET['type'] ?? 'hsn';
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $branch_id = $_SESSION['branch_id'] ?? 1;

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="GST_' . $type . '_' . $month . '_' . $year . '.csv"');

        $output = fopen('php://output', 'w');

        if ($type === 'hsn') {
            fputcsv($output, ['HSN Code', 'Unit', 'Total Quantity', 'Total Value', 'Taxable Value', 'Tax Rate (%)', 'Tax Amount']);
            $stmt = $this->db->query("
                SELECT p.hsn_code, p.unit, SUM(ii.qty), SUM(ii.total), SUM(ii.qty * ii.unit_price), ii.tax_percent, SUM(ii.tax_amount)
                FROM invoice_items ii JOIN products p ON ii.product_id = p.id JOIN invoices i ON ii.invoice_id = i.id
                WHERE i.branch_id = ? AND i.status = 'paid' AND MONTH(i.created_at) = ? AND YEAR(i.created_at) = ?
                GROUP BY p.hsn_code, ii.tax_percent, p.unit
            ", [$branch_id, $month, $year]);
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                fputcsv($output, array_values($row));
                ob_flush(); flush(); // Phase 8: Memory efficient streaming [#41]
            }
        } else {
            fputcsv($output, ['Tax Rate (%)', 'Taxable Value', 'CGST', 'SGST', 'Total Tax']);
            $stmt = $this->db->query("
                SELECT tax_percent, SUM(qty * unit_price), SUM(tax_amount)/2, SUM(tax_amount)/2, SUM(tax_amount)
                FROM invoice_items ii JOIN invoices i ON ii.invoice_id = i.id
                WHERE i.branch_id = ? AND i.status = 'paid' AND MONTH(i.created_at) = ? AND YEAR(i.created_at) = ?
                GROUP BY tax_percent
            ", [$branch_id, $month, $year]);
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                fputcsv($output, array_values($row));
                ob_flush(); flush();
            }
        }
        fclose($output);
        exit;
    }

    public function priceComparison()
    {
        $this->requireRole([1, 2]); // Admin & Manager
        $productId = $_GET['product_id'] ?? null;
        $comparisonData = [];
        $product = null;
        $history = [];

        // Search for products if 'q' is set
        if (isset($_GET['q'])) {
            $q = $_GET['q'];
            $results = $this->db->query("SELECT id, name, sku FROM products WHERE name LIKE ? OR sku LIKE ? LIMIT 10", ["%$q%", "%$q%"])->fetchAll();
            header('Content-Type: application/json');
            echo json_encode($results);
            exit;
        }

        if ($productId) {
            $product = $this->db->query("SELECT * FROM products WHERE id = ?", [$productId])->fetch();

            if ($product) {
                // Aggregate Price Data by Vendor
                $comparisonData = $this->db->query("
                    SELECT 
                        v.name as vendor_name,
                        MIN(poi.unit_price) as min_price,
                        MAX(poi.unit_price) as max_price,
                        AVG(poi.unit_price) as avg_price,
                        (SELECT unit_price FROM purchase_order_items poi2 
                         JOIN purchase_orders po2 ON poi2.purchase_order_id = po2.id 
                         WHERE poi2.product_id = ? AND po2.vendor_id = v.id 
                         ORDER BY po2.created_at DESC LIMIT 1) as last_price,
                        MAX(po.created_at) as last_purchase_date
                    FROM purchase_order_items poi
                    JOIN purchase_orders po ON poi.purchase_order_id = po.id
                    JOIN vendors v ON po.vendor_id = v.id
                    WHERE poi.product_id = ?
                    GROUP BY v.id, v.name
                    ORDER BY last_price ASC
                ", [$productId, $productId])->fetchAll();

                // Detailed History
                $history = $this->db->query("
                    SELECT 
                        po.id,
                        po.po_number,
                        v.name as vendor_name,
                        poi.unit_price,
                        poi.qty,
                        po.created_at
                    FROM purchase_order_items poi
                    JOIN purchase_orders po ON poi.purchase_order_id = po.id
                    JOIN vendors v ON po.vendor_id = v.id
                    WHERE poi.product_id = ?
                    ORDER BY po.created_at DESC
                    LIMIT 20
                ", [$productId])->fetchAll();
            }
        }

        return $this->view('reports/price_comparison', [
            'product' => $product,
            'comparisonData' => $comparisonData,
            'history' => $history
        ], 'dashboard');
    }
}
