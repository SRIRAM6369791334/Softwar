<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class PurchaseOrderController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole([1, 2]); // Admin & Manager
    }

    public function index()
    {
        $branchId = Auth::getCurrentBranch();
        $pos = $this->db->query("
            SELECT po.*, v.name as vendor_name, u.full_name as created_by_name
            FROM purchase_orders po
            JOIN vendors v ON po.vendor_id = v.id
            JOIN users u ON po.created_by = u.id
            WHERE po.branch_id = ?
            ORDER BY po.created_at DESC
        ", [$branchId])->fetchAll();

        return $this->view('inventory/po/index', ['orders' => $pos], 'dashboard');
    }

    public function create()
    {
        $branchId = Auth::getCurrentBranch();
        $vendors = $this->db->query("SELECT id, name FROM vendors WHERE is_active = 1")->fetchAll();
        $products = $this->db->query("SELECT id, name, sku FROM products WHERE is_active = 1 AND branch_id = ?", [$branchId])->fetchAll();

        return $this->view('inventory/po/create', [
            'vendors' => $vendors,
            'products' => $products
        ], 'dashboard');
    }

    public function store()
    {
        $branchId = Auth::getCurrentBranch();
        $userId = Auth::id();
        $orderNo = 'PO-' . time();
        $productIds = $_POST['product_id'];
        $qtys = $_POST['qty'];
        $prices = $_POST['price'];

        try {
            $pdo = $this->db->getConnection();
            $pdo->beginTransaction();

            // 1. Calculate Total
            $total = 0;
            foreach ($productIds as $i => $pid) {
                $total += $qtys[$i] * $prices[$i];
            }

            // 2. Insert PO Header
            $this->db->query("
                INSERT INTO purchase_orders (vendor_id, branch_id, order_no, total_amount, status, created_by)
                VALUES (?, ?, ?, ?, 'pending', ?)
            ", [$_POST['vendor_id'], $branchId, $orderNo, $total, $userId]);

            $poId = $pdo->lastInsertId();

            // 3. Insert PO Items
            foreach ($productIds as $i => $pid) {
                $this->db->query("
                    INSERT INTO purchase_order_items (po_id, product_id, qty, estimated_price)
                    VALUES (?, ?, ?, ?)
                ", [$poId, $pid, $qtys[$i], $prices[$i]]);
            }

            $pdo->commit();
            $this->redirect('/inventory/po?success=Purchase Order created successfully');

        } catch (\Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }
    }

    public function transitionStatus($id, $status)
    {
        // Simple status update for workflow
        $this->db->query("UPDATE purchase_orders SET status = ? WHERE id = ?", [$status, $id]);
        $this->redirect('/inventory/po?success=Status updated to ' . $status);
    }
}
