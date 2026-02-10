<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class InventoryController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole([1, 2]); // Admin & Manager
    }

    public function inward()
    {
        // Get all products for the dropdown (filtered by current branch)
        $branchId = Auth::getCurrentBranch();
        $products = $this->db->query(
            "SELECT id, name, sku FROM products WHERE is_active = 1 AND branch_id = ? ORDER BY name ASC", 
            [$branchId]
        )->fetchAll();
        return $this->view('inventory/inward', ['products' => $products], 'dashboard');
    }

    public function store()
    {
        // 1. Inputs
        $product_id = $_POST['product_id'];
        $batch_no = $_POST['batch_no'];
        $expiry = $_POST['expiry_date'] ?: null;
        $qty = $_POST['quantity'];
        $cost = $_POST['purchase_price'];
        $mrp = $_POST['mrp'];
        $sale_price = $_POST['sale_price'];

        // 2. Validate
        if ($qty <= 0) die("Error: Quantity must be greater than 0");

        // 3. Create Batch Entry (with branch_id)
        $branchId = Auth::getCurrentBranch();
        $sql = "INSERT INTO product_batches 
                (product_id, batch_no, expiry_date, purchase_price, mrp, sale_price, stock_qty, branch_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [$product_id, $batch_no, $expiry, $cost, $mrp, $sale_price, $qty, $branchId]);

        // 4. Log Movement (Ledger - Future Implementation, currently simple batching)
        // In a full system, we would insert into 'inventory_ledger' here too.

        $this->redirect('/products'); // Back to product list to see updated stock
    }
    
    public function index() {
        // Show all active batches (filtered by current branch)
        $branchId = Auth::getCurrentBranch();
        $sql = "
            SELECT pb.*, p.name as product_name, p.sku 
            FROM product_batches pb
            JOIN products p ON pb.product_id = p.id
            WHERE pb.stock_qty > 0 AND pb.branch_id = ?
            ORDER BY pb.expiry_date ASC
        ";
        $batches = $this->db->query($sql, [$branchId])->fetchAll();
        return $this->view('inventory/index', ['batches' => $batches], 'dashboard');
    }
}
