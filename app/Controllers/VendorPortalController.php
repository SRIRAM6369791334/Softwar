<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class VendorPortalController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function login()
    {
        if (Auth::vendorCheck()) {
            $this->redirect('/vendor/dashboard');
        }
        return $this->view('vendor/login', [], 'vendor_auth');
    }

    public function authenticate()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $vendor = $this->db->query("SELECT * FROM vendors WHERE email = ? AND is_active = 1", [$email])->fetch();

        if ($vendor && password_verify($password, $vendor['password_hash'])) {
            Auth::vendorLogin($vendor);
            $this->redirect('/vendor/dashboard');
        }

        return $this->view('vendor/login', ['error' => 'Invalid credentials or inactive account'], 'vendor_auth');
    }

    public function dashboard()
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');

        $vendorId = Auth::vendorId();
        
        // Stats
        $pendingPOs = $this->db->query("
            SELECT COUNT(*) as count 
            FROM purchase_orders 
            WHERE vendor_id = ? AND status IN ('pending', 'ordered')
        ", [$vendorId])->fetch();

        $deliveredThisMonth = $this->db->query("
            SELECT COUNT(*) as count 
            FROM purchase_orders 
            WHERE vendor_id = ? AND status = 'delivered' 
            AND created_at >= ?", 
            [$vendorId, date('Y-m-01')]
        )->fetch();

        // Recent Orders
        $recentOrders = $this->db->query("
            SELECT po.*, b.name as branch_name 
            FROM purchase_orders po
            JOIN branches b ON po.branch_id = b.id
            WHERE po.vendor_id = ?
            ORDER BY po.created_at DESC
            LIMIT 5
        ", [$vendorId])->fetchAll();

        // Broadcasts
        $broadcasts = $this->db->query("
            SELECT * FROM vendor_broadcasts 
            ORDER BY created_at DESC 
            LIMIT 3
        ")->fetchAll();

        return $this->view('vendor/dashboard', [
            'pending_count' => $pendingPOs['count'] ?? 0,
            'delivered_month' => $deliveredThisMonth['count'] ?? 0,
            'recent_orders' => $recentOrders,
            'broadcasts' => $broadcasts
        ], 'vendor');
    }

    public function orders()
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');

        $vendorId = Auth::vendorId();
        $orders = $this->db->query("
            SELECT po.*, b.name as branch_name 
            FROM purchase_orders po
            JOIN branches b ON po.branch_id = b.id
            WHERE po.vendor_id = ?
            ORDER BY po.created_at DESC
        ", [$vendorId])->fetchAll();

        return $this->view('vendor/orders', ['orders' => $orders], 'vendor');
    }

    public function orderDetails($id)
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');

        $vendorId = Auth::vendorId();
        $order = $this->db->query("
            SELECT po.*, b.name as branch_name 
            FROM purchase_orders po
            JOIN branches b ON po.branch_id = b.id
            WHERE po.id = ? AND po.vendor_id = ?
        ", [$id, $vendorId])->fetch();

        if (!$order) die("Order not found");

        $items = $this->db->query("
            SELECT poi.*, p.name as product_name, p.sku, p.unit
            FROM purchase_order_items poi
            JOIN products p ON poi.product_id = p.id
            WHERE poi.po_id = ?
        ", [$id])->fetchAll();

        return $this->view('vendor/order_details', [
            'order' => $order,
            'items' => $items
        ], 'vendor');
    }

    public function uploadInvoice($id)
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');

        $vendorId = Auth::vendorId();
        $order = $this->db->query("SELECT id FROM purchase_orders WHERE id = ? AND vendor_id = ?", [$id, $vendorId])->fetch();

        if (!$order) die("Unauthorized");

        if (isset($_FILES['invoice']) && $_FILES['invoice']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['invoice']['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) !== 'pdf') die("Error: Only PDF invoices allowed");

            $filename = 'invoice_' . $id . '_' . time() . '.pdf';
            $uploadDir = APP_ROOT . '/public/uploads/invoices/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            move_uploaded_file($_FILES['invoice']['tmp_name'], $uploadDir . $filename);

            $this->db->query("UPDATE purchase_orders SET invoice_pdf = ? WHERE id = ?", [$filename, $id]);
        }

        $this->redirect('/vendor/orders/' . $id . '?success=Invoice uploaded');
    }

    public function forecasting()
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');
        $vendorId = Auth::vendorId();

        // Calculate 7-day run-out based on last 30 days of sales
        // This is a simplified version for Phase 10
        $sql = "
            SELECT 
                p.id, p.name, p.sku, p.unit,
                COALESCE(SUM(pb.stock_qty), 0) as current_stock,
                COALESCE(sales.avg_daily_sales, 0) as avg_daily_sales
            FROM products p
            LEFT JOIN product_batches pb ON p.id = pb.product_id
            LEFT JOIN (
                SELECT product_id, SUM(qty)/30 as avg_daily_sales
                FROM invoice_items ii
                JOIN invoices i ON ii.invoice_id = i.id
                WHERE i.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY product_id
            ) sales ON p.id = sales.product_id
            WHERE p.id IN (
                SELECT product_id FROM purchase_order_items poi 
                JOIN purchase_orders po ON poi.po_id = po.id 
                WHERE po.vendor_id = ?
            )
            GROUP BY p.id
            HAVING current_stock < (avg_daily_sales * 7)
        ";
        $forecast = $this->db->query($sql, [$vendorId])->fetchAll();

        return $this->view('vendor/forecasting', ['forecast' => $forecast], 'vendor');
    }

    public function quotations()
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');
        $vendorId = Auth::vendorId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $proposedPrice = $_POST['proposed_price'];
            
            // Get current price from latest batch or product
            $current = $this->db->query("SELECT purchase_price FROM product_batches WHERE product_id = ? ORDER BY created_at DESC LIMIT 1", [$productId])->fetch();
            $currentPrice = $current['purchase_price'] ?? 0;

            $this->db->query("
                INSERT INTO vendor_quotations (vendor_id, product_id, proposed_price, current_price, status)
                VALUES (?, ?, ?, ?, 'pending')
            ", [$vendorId, $productId, $proposedPrice, $currentPrice]);

            $this->redirect('/vendor/quotations?success=Quotation submitted');
        }

        $quotations = $this->db->query("
            SELECT vq.*, p.name as product_name, p.sku 
            FROM vendor_quotations vq
            JOIN products p ON vq.product_id = p.id
            WHERE vq.vendor_id = ?
            ORDER BY vq.created_at DESC
        ", [$vendorId])->fetchAll();

        // Products for dropdown
        $products = $this->db->query("
            SELECT DISTINCT p.id, p.name, p.sku 
            FROM products p
            JOIN purchase_order_items poi ON p.id = poi.product_id
            JOIN purchase_orders po ON poi.po_id = po.id
            WHERE po.vendor_id = ?
        ", [$vendorId])->fetchAll();

        return $this->view('vendor/quotations', [
            'quotations' => $quotations,
            'products' => $products
        ], 'vendor');
    }

    public function ledger()
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');
        $vendorId = Auth::vendorId();

        $entries = $this->db->query("
            SELECT vl.*, po.order_no 
            FROM vendor_ledger vl
            LEFT JOIN purchase_orders po ON vl.po_id = po.id
            WHERE vl.vendor_id = ?
            ORDER BY vl.created_at DESC
        ", [$vendorId])->fetchAll();

        // Calculate Balance
        $summary = $this->db->query("
            SELECT 
                SUM(CASE WHEN type='credit' THEN amount ELSE 0 END) as total_owed,
                SUM(CASE WHEN type='debit' THEN amount ELSE 0 END) as total_paid
            FROM vendor_ledger 
            WHERE vendor_id = ?
        ", [$vendorId])->fetch();

        return $this->view('vendor/ledger', [
            'entries' => $entries,
            'summary' => $summary
        ], 'vendor');
    }

    public function updateBackorder($id)
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');
        $status = $_POST['status'];
        $this->db->query("UPDATE purchase_orders SET backorder_status = ? WHERE id = ?", [$status, $id]);
        $this->redirect('/vendor/orders/' . $id . '?success=Backorder status updated');
    }

    public function saveGRN($id)
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');
        $signature = $_POST['signature'] ?? null;
        
        $photoPath = null;
        if (isset($_FILES['grn_photo']) && $_FILES['grn_photo']['error'] === UPLOAD_ERR_OK) {
            $filename = 'grn_' . $id . '_' . time() . '.jpg';
            move_uploaded_file($_FILES['grn_photo']['tmp_name'], APP_ROOT . '/public/uploads/grn/' . $filename);
            $photoPath = $filename;
        }

        $this->db->query("
            UPDATE purchase_orders 
            SET grn_signature = ?, grn_photo = ?, status = 'delivered' 
            WHERE id = ?
        ", [$signature, $photoPath, $id]);

        $this->redirect('/vendor/orders/' . $id . '?success=Delivery confirmed');
    }

    public function analytics()
    {
        if (!Auth::vendorCheck()) $this->redirect('/vendor/login');
        $vendorId = Auth::vendorId();

        // 1. Financial Speedo Data
        $summary = $this->db->query("
            SELECT 
                COALESCE(SUM(CASE WHEN type='credit' THEN amount ELSE 0 END), 0) as total_owed,
                COALESCE(SUM(CASE WHEN type='debit' THEN amount ELSE 0 END), 0) as total_paid
            FROM vendor_ledger 
            WHERE vendor_id = ?
        ", [$vendorId])->fetch();

        // 2. Delivery Heatmap (Daily counts for last 30 days)
        $heatmapData = $this->db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM purchase_orders 
            WHERE vendor_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
        ", [$vendorId])->fetchAll();

        // 3. Stock Run-out Gauges (Top 4 products)
        $gauges = $this->db->query("
            SELECT 
                p.name,
                COALESCE(SUM(pb.stock_qty), 0) as current_stock,
                COALESCE(sales.avg_daily_sales, 0) as avg_daily_sales
            FROM products p
            LEFT JOIN product_batches pb ON p.id = pb.product_id
            LEFT JOIN (
                SELECT product_id, SUM(qty)/30 as avg_daily_sales
                FROM invoice_items ii
                JOIN invoices i ON ii.invoice_id = i.id
                WHERE i.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY product_id
            ) sales ON p.id = sales.product_id
            WHERE p.id IN (
                SELECT product_id FROM purchase_order_items poi 
                JOIN purchase_orders po ON poi.po_id = po.id 
                WHERE po.vendor_id = ?
            )
            GROUP BY p.id
            ORDER BY avg_daily_sales DESC
            LIMIT 4
        ", [$vendorId])->fetchAll();

        return $this->view('vendor/analytics', [
            'summary' => $summary,
            'heatmap' => $heatmapData,
            'gauges' => $gauges
        ], 'vendor');
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/vendor/login');
    }
}
