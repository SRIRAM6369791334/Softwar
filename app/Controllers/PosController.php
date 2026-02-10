<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class PosController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function terminal()
    {
        // Load the specialized POS interface (no sidebar layout for max screen space)
        return $this->view('pos/terminal', [], 'dashboard'); 
    }

    public function search()
    {
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 2) {
            $this->json([]);
            return;
        }

        // Search by Name or SKU (filtered by current branch)
        // Join with product_batches to get pricing and stock
        // We take the 'FIFO' (First Expiring) batch that has stock
        $branchId = Auth::getCurrentBranch();
        $sql = "
            SELECT p.id, p.name, p.sku, p.unit, 
                   pb.id as batch_id, pb.sale_price, pb.stock_qty, pb.expiry_date,
                   tg.percentage as tax_percent
            FROM products p
            JOIN product_batches pb ON p.id = pb.product_id
            JOIN tax_groups tg ON p.tax_group_id = tg.id
            WHERE (p.name LIKE ? OR p.sku LIKE ?) 
            AND pb.stock_qty > 0
            AND p.branch_id = ?
            AND pb.branch_id = ?
            ORDER BY pb.expiry_date ASC
            LIMIT 10
        ";

        $results = $this->db->query($sql, ["%$query%", "$query%", $branchId, $branchId])->fetchAll();
        $this->json($results);
    }

    public function checkout()
    {
        // Receive JSON payload
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['items'])) {
            $this->json(['status' => 'error', 'message' => 'Cart is empty']);
            return;
        }

        try {
            $pdo = $this->db->getConnection();
            $pdo->beginTransaction();

            // 1. Create Invoice Header (with branch_id)
            $invNo = 'INV-' . date('Ymd') . '-' . time();
            $userId = $_SESSION['user_id'];
            $branchId = Auth::getCurrentBranch();
            
            $stmt = $this->db->query(
                "INSERT INTO invoices (user_id, invoice_no, sub_total, tax_total, grand_total, payment_mode, branch_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$userId, $invNo, $input['subTotal'], $input['taxTotal'], $input['grandTotal'], 'cash', $branchId]
            );
            
            $invoiceId = $pdo->lastInsertId();

            // 2. Process Items & Deduct Stock
            foreach ($input['items'] as $item) {
                // Determine Batch (In real app, front-end selects batch or backend picks FIFO)
                // Here we rely on the batch_id sent from search
                
                // Simple Stock Check
                $check = $this->db->query("SELECT stock_qty FROM product_batches WHERE id = ?", [$item['batch_id']])->fetch();
                if ($check['stock_qty'] < $item['qty']) {
                    throw new \Exception("Insufficient stock for " . $item['name']);
                }

                // Deduct Stock
                $this->db->query(
                    "UPDATE product_batches SET stock_qty = stock_qty - ? WHERE id = ?", 
                    [$item['qty'], $item['batch_id']]
                );

                // Add Invoice Item
                $this->db->query(
                    "INSERT INTO invoice_items (invoice_id, product_id, batch_id, qty, unit_price, tax_percent, tax_amount, total)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $invoiceId, 
                        $item['id'], 
                        $item['batch_id'], 
                        $item['qty'], 
                        $item['sale_price'], // Saved at moment of sale
                        $item['tax_percent'],
                        $item['tax_amount'],
                        $item['line_total']
                    ]
                );
            }

            $pdo->commit();
            $this->json(['status' => 'success', 'invoice_no' => $invNo]);

        } catch (\Exception $e) {
            $pdo->rollBack();
            $this->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
