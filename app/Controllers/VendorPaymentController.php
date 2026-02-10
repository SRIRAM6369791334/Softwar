<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class VendorPaymentController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole([1, 2]); // Admin & Manager
    }

    public function index()
    {
        $payments = $this->db->query("
            SELECT vp.*, v.name as vendor_name, po.order_no, u.full_name as created_by_name
            FROM vendor_payments vp
            JOIN vendors v ON vp.vendor_id = v.id
            LEFT JOIN purchase_orders po ON vp.po_id = po.id
            JOIN users u ON vp.created_by = u.id
            ORDER BY vp.payment_date DESC
        ")->fetchAll();

        return $this->view('inventory/vendors/payments', ['payments' => $payments], 'dashboard');
    }

    public function store()
    {
        $vendorId = $_POST['vendor_id'];
        $poId = $_POST['po_id'] ?: null;
        $amount = $_POST['amount'];
        $date = $_POST['payment_date'];
        $mode = $_POST['payment_mode'];
        $ref = $_POST['reference_no'];
        $userId = Auth::id();

        // RECONCILIATION CHECK [#57]
        if ($poId) {
            $po = $this->db->query("SELECT total_amount FROM purchase_orders WHERE id = ?", [$poId])->fetch();
            $paid = $this->db->query("SELECT SUM(amount) as total FROM vendor_payments WHERE po_id = ?", [$poId])->fetch();
            
            if ($paid['total'] + $amount > $po['total_amount'] + 0.01) {
                 die("Error: Total payments exceed Purchase Order amount. Reconciliation failed.");
            }
        }

        $this->db->query(
            "INSERT INTO vendor_payments (vendor_id, po_id, amount, payment_date, payment_mode, reference_no, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$vendorId, $poId, $amount, $date, $mode, $ref, $userId]
        );

        $this->redirect('/inventory/vendors/payments?success=Payment recorded and reconciled');
    }
}
