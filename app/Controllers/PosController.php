<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class PosController extends Controller
{
    private $db;

    private $checkoutService;

    public function __construct(Database $db, \App\Services\CheckoutService $checkoutService)
    {
        $this->db = $db;
        $this->checkoutService = $checkoutService;
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

        // Search by Name or SKU/Barcode in variants
        $branchId = Auth::getCurrentBranch();
        $sql = "
            SELECT p.id as product_id, p.name as product_name, 
                   pv.id as variant_id, pv.variant_name, pv.barcode, pv.sku_code as sku,
                   pv.selling_price as sale_price, pv.current_stock as stock_qty,
                   tg.percentage as tax_percent
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            LEFT JOIN tax_groups tg ON pv.tax_slab_id = tg.id
            WHERE (p.name LIKE ? OR pv.barcode = ? OR pv.sku_code = ?) 
            AND pv.current_stock > 0
            AND p.branch_id = ?
            AND p.is_active = 1
            AND pv.is_active = 1
            AND p.deleted_at IS NULL
            ORDER BY p.name ASC
            LIMIT 20
        ";

        $results = $this->db->query($sql, ["%$query%", $query, $query, $branchId])->fetchAll();
        
        // Map to expected format for POS frontend
        $formatted = array_map(function($row) {
            return [
                'id' => $row['product_id'],
                'name' => $row['product_name'] . ($row['variant_name'] ? " - " . $row['variant_name'] : ""),
                'sku' => $row['sku'],
                'batch_id' => $row['variant_id'], // Map variant_id to batch_id for compat
                'sale_price' => $row['sale_price'],
                'stock_qty' => $row['stock_qty'],
                'tax_percent' => $row['tax_percent']
            ];
        }, $results);

        $this->json($formatted);
    }

    public function checkout()
    {
        // Receive JSON payload via abstraction
        $input = $this->request ? $this->request->getJson() : json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['items']) || !is_array($input['items'])) {
            return $this->errorResponse('Cart is empty or invalid', 400);
        }

        try {
            // Use Injected Service
            $userId = $_SESSION['user_id'];
            $branchId = Auth::getCurrentBranch();

            $result = $this->checkoutService->processTransaction($input['items'], $userId, $branchId);
            
            // Standardize Success Response
            // CheckoutService returns ['status' => 'success', 'invoice_no' => ...]
            // We can wrap this data
            return $this->successResponse($result, 'Transaction successful');

        } catch (\Exception $e) {
            // Log is handled by outer handler or we log here
            error_log("Checkout Service Error: " . $e->getMessage());
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
