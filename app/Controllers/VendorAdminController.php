<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class VendorAdminController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $vendors = $this->db->query("SELECT * FROM vendors ORDER BY name")->fetchAll();
        return $this->view('admin/vendor/index', ['vendors' => $vendors], 'dashboard');
    }

    public function create()
    {
        return $this->view('admin/vendor/create', [], 'dashboard');
    }

    public function store()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $phone = $_POST['phone'];
        
        $this->db->query("INSERT INTO vendors (name, email, password_hash, phone) VALUES (?, ?, ?, ?)", 
            [$name, $email, $password, $phone]);

        $this->redirect('/admin/vendors?success=Vendor created');
    }

    public function quotations()
    {
        $quotations = $this->db->query("
            SELECT vq.*, v.name as vendor_name, p.name as product_name 
            FROM vendor_quotations vq
            JOIN vendors v ON vq.vendor_id = v.id
            JOIN products p ON vq.product_id = p.id
            WHERE vq.status = 'pending'
            ORDER BY vq.created_at DESC
        ")->fetchAll();

        return $this->view('admin/vendor/quotations', ['quotations' => $quotations], 'dashboard');
    }

    public function approveQuotation($id)
    {
        $vq = $this->db->query("SELECT * FROM vendor_quotations WHERE id = ?", [$id])->fetch();
        if (!$vq) die("Quotation not found");

        $this->db->query("UPDATE vendor_quotations SET status = 'approved' WHERE id = ?", [$id]);
        
        // Note: Prices are updated on next PO creation based on this approval in a real system
        // For now, we just mark it approved.

        $this->redirect('/admin/vendor/quotations?success=Approved');
    }

    public function broadcast()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->db->query("
                INSERT INTO vendor_broadcasts (title, message, created_by)
                VALUES (?, ?, ?)
            ", [$_POST['title'], $_POST['message'], Auth::id()]);

            $this->redirect('/admin/vendor/broadcast?success=Broadcast sent');
        }

        return $this->view('admin/vendor/broadcast', [], 'dashboard');
    }

    public function payments()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->db->query("
                INSERT INTO vendor_ledger (vendor_id, po_id, type, amount, description, reference_no)
                VALUES (?, ?, 'debit', ?, ?, ?)
            ", [$_POST['vendor_id'], $_POST['po_id'] ?: null, $_POST['amount'], $_POST['description'], $_POST['reference_no']]);

            $this->redirect('/admin/vendor/payments?success=Payment recorded');
        }

        $vendors = $this->db->query("SELECT id, name FROM vendors")->fetchAll();
        return $this->view('admin/vendor/payments', ['vendors' => $vendors], 'dashboard');
    }

    public function analytics()
    {
        // 1. Reliability Index (Orders vs Received)
        $reliability = $this->db->query("
            SELECT 
                v.name as vendor_name,
                COUNT(po.id) as total_orders,
                SUM(CASE WHEN po.status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders
            FROM vendors v
            LEFT JOIN purchase_orders po ON v.id = po.vendor_id
            GROUP BY v.id
        ")->fetchAll();

        // 2. Price Trends (Approved quotations)
        $priceTrends = $this->db->query("
            SELECT 
                vq.created_at,
                vq.proposed_price,
                p.name as product_name
            FROM vendor_quotations vq
            JOIN products p ON vq.product_id = p.id
            WHERE vq.status = 'approved'
            ORDER BY vq.created_at ASC
            LIMIT 20
        ")->fetchAll();

        // 3. Regional Supply Distribution
        $regionalDist = $this->db->query("
            SELECT 
                b.region,
                COUNT(po.id) as total_orders,
                SUM(po.total_amount) as total_value
            FROM branches b
            LEFT JOIN purchase_orders po ON b.id = po.branch_id
            GROUP BY b.region
        ")->fetchAll();

        return $this->view('admin/vendor/analytics', [
            'reliability' => $reliability,
            'priceTrends' => $priceTrends,
            'regionalDist' => $regionalDist
        ], 'dashboard');
    }
}
