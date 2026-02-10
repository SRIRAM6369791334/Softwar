<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class StockTransferController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List transfers related to current branch
     */
    public function index()
    {
        $branchId = Auth::getCurrentBranch();
        
        $sql = "
            SELECT st.*, 
                   fb.name as from_branch, tb.name as to_branch,
                   p.name as product_name, p.sku,
                   u.full_name as created_by_name
            FROM stock_transfers st
            JOIN branches fb ON st.from_branch_id = fb.id
            JOIN branches tb ON st.to_branch_id = tb.id
            JOIN products p ON st.product_id = p.id
            LEFT JOIN users u ON st.created_by = u.id
            WHERE st.from_branch_id = ? OR st.to_branch_id = ?
            ORDER BY st.created_at DESC
        ";
        
        $transfers = $this->db->query($sql, [$branchId, $branchId])->fetchAll();
        
        return $this->view('inventory/transfers/index', [
            'transfers' => $transfers
        ], 'dashboard');
    }

    /**
     * Form to create new transfer
     */
    public function create()
    {
        $branchId = Auth::getCurrentBranch();
        
        // Only active branches except current
        $branches = $this->db->query("SELECT id, name FROM branches WHERE is_active = 1 AND id != ?", [$branchId])->fetchAll();
        
        // Products with stock in current branch
        $products = $this->db->query("
            SELECT DISTINCT p.id, p.name, p.sku
            FROM products p
            JOIN product_batches pb ON p.id = pb.product_id
            WHERE pb.branch_id = ? AND pb.stock_qty > 0
        ", [$branchId])->fetchAll();

        return $this->view('inventory/transfers/create', [
            'branches' => $branches,
            'products' => $products
        ], 'dashboard');
    }

    /**
     * Get batches for a product in current branch (for AJAX)
     */
    public function getBatches($productId)
    {
        $branchId = Auth::getCurrentBranch();
        $batches = $this->db->query("
            SELECT id, batch_no, stock_qty, expiry_date, sale_price
            FROM product_batches
            WHERE product_id = ? AND branch_id = ? AND stock_qty > 0
        ", [$productId, $branchId])->fetchAll();
        
        $this->json($batches);
    }

    /**
     * Store transfer or Request
     */
    public function store()
    {
        $input = $_POST;
        $branchId = Auth::getCurrentBranch();
        $transferNo = 'TRF-' . time();
        $userId = $_SESSION['user_id'];
        $type = $input['type'] ?? 'direct'; // direct or request

        try {
            $pdo = $this->db->getConnection();
            $pdo->beginTransaction();

            if ($type == 'direct') {
                // 1. Verify availability
                $batch = $this->db->query("SELECT stock_qty FROM product_batches WHERE id = ?", [$input['batch_id']])->fetch();
                if (!$batch || $batch['stock_qty'] < $input['qty']) {
                    throw new \Exception("Insufficient stock in selected batch.");
                }

                // 2. Deduct from source branch batch
                $this->db->query(
                    "UPDATE product_batches SET stock_qty = stock_qty - ? WHERE id = ?",
                    [$input['qty'], $input['batch_id']]
                );

                // 3. Record transfer
                $this->db->query("
                    INSERT INTO stock_transfers (
                        from_branch_id, to_branch_id, product_id, batch_id, qty, 
                        status, transfer_no, created_by, remarks
                    ) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?)
                ", [
                    $branchId, $input['to_branch_id'], $input['product_id'], 
                    $input['batch_id'], $input['qty'], $transferNo, $userId, $input['remarks']
                ]);
            } else {
                // Request Type: Only product and qty needed, no batch deduction yet
                $this->db->query("
                    INSERT INTO stock_transfers (
                        from_branch_id, to_branch_id, product_id, batch_id, qty, 
                        status, transfer_no, created_by, remarks
                    ) VALUES (?, ?, ?, ?, ?, 'requested', ?, ?, ?)
                ", [
                    $input['from_branch_id'], $branchId, $input['product_id'], 
                    0, $input['qty'], $transferNo, $userId, $input['remarks']
                ]);
            }

            $pdo->commit();
            $this->redirect('/inventory/transfers?success=Transfer/Request initiated');

        } catch (\Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Form for supplier to fulfill a request
     */
    public function fulfillForm($id)
    {
        $branchId = Auth::getCurrentBranch();
        $transfer = $this->db->query("
            SELECT st.*, p.name as product_name, p.sku, b.name as requester_branch
            FROM stock_transfers st
            JOIN products p ON st.product_id = p.id
            JOIN branches b ON st.to_branch_id = b.id
            WHERE st.id = ? AND st.from_branch_id = ? AND st.status = 'requested'
        ", [$id, $branchId])->fetch();

        if (!$transfer) die("Request not found or not assigned to your branch.");

        // Load available batches in current branch for this product
        $batches = $this->db->query("
            SELECT id, batch_no, stock_qty, expiry_date 
            FROM product_batches 
            WHERE product_id = ? AND branch_id = ? AND stock_qty > 0
        ", [$transfer['product_id'], $branchId])->fetchAll();

        return $this->view('inventory/transfers/fulfill', [
            'transfer' => $transfer,
            'batches' => $batches
        ], 'dashboard');
    }

    /**
     * Fulfill the request: Deduct stock and move to pending
     */
    public function fulfill($id)
    {
        $branchId = Auth::getCurrentBranch();
        $batchId = $_POST['batch_id'];
        $qty = $_POST['qty'];

        try {
            $pdo = $this->db->getConnection();
            $pdo->beginTransaction();

            // 1. Double check stock
            $batch = $this->db->query("SELECT stock_qty FROM product_batches WHERE id = ?", [$batchId])->fetch();
            if (!$batch || $batch['stock_qty'] < $qty) throw new \Exception("Insufficient stock locally.");

            // 2. Deduct stock
            $this->db->query("UPDATE product_batches SET stock_qty = stock_qty - ? WHERE id = ?", [$qty, $batchId]);

            // 3. Update transfer status and link to batch
            $this->db->query(
                "UPDATE stock_transfers SET batch_id = ?, status = 'pending', qty = ? WHERE id = ?",
                [$batchId, $qty, $id]
            );

            $pdo->commit();
            $this->redirect('/inventory/transfers?success=Request fulfilled and items in transit');

        } catch (\Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Receive transfer at destination branch
     */
    public function receive($id)
    {
        $branchId = Auth::getCurrentBranch();
        $userId = $_SESSION['user_id'];

        try {
            $pdo = $this->db->getConnection();
            $pdo->beginTransaction();

            // 1. Get transfer details
            $transfer = $this->db->query("
                SELECT st.*, pb.batch_no, pb.expiry_date, pb.purchase_price, pb.mrp, pb.sale_price
                FROM stock_transfers st
                JOIN product_batches pb ON st.batch_id = pb.id
                WHERE st.id = ? AND st.to_branch_id = ? AND st.status = 'pending'
            ", [$id, $branchId])->fetch();

            if (!$transfer) {
                throw new \Exception("Transfer not found or already processed.");
            }

            // 2. Create/Update batch in destination branch
            // Check if same batch already exists in destination
            $destBatch = $this->db->query("
                SELECT id FROM product_batches 
                WHERE product_id = ? AND batch_no = ? AND branch_id = ?
            ", [$transfer['product_id'], $transfer['batch_no'], $branchId])->fetch();

            if ($destBatch) {
                $this->db->query(
                    "UPDATE product_batches SET stock_qty = stock_qty + ? WHERE id = ?",
                    [$transfer['qty'], $destBatch['id']]
                );
            } else {
                $this->db->query("
                    INSERT INTO product_batches (
                        product_id, batch_no, expiry_date, purchase_price, 
                        mrp, sale_price, stock_qty, branch_id
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ", [
                    $transfer['product_id'], $transfer['batch_no'], $transfer['expiry_date'],
                    $transfer['purchase_price'], $transfer['mrp'], $transfer['sale_price'],
                    $transfer['qty'], $branchId
                ]);
            }

            // 3. Update transfer status
            $this->db->query(
                "UPDATE stock_transfers SET status = 'completed', received_by = ? WHERE id = ?",
                [$userId, $id]
            );

            $pdo->commit();
            $this->redirect('/inventory/transfers?success=Transferred items received');

        } catch (\Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }
    }
}
