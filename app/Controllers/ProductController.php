<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class ProductController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function import()
    {
        return $this->view('products/import', [], 'dashboard');
    }

    public function index()
    {
        // List products with their relationships
        $branchId = Auth::getCurrentBranch();
        $sql = "
            SELECT p.*, c.name as category_name, b.name as brand_name,
            (SELECT SUM(current_stock) FROM product_variants WHERE product_id = p.id) as total_stock,
            (SELECT MIN(selling_price) FROM product_variants WHERE product_id = p.id) as min_price,
            (SELECT MAX(selling_price) FROM product_variants WHERE product_id = p.id) as max_price
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.branch_id = ? AND p.deleted_at IS NULL
            ORDER BY p.name ASC
        ";
        $products = $this->db->query($sql, [$branchId])->fetchAll();
        
        return $this->view('products/index', ['products' => $products], 'dashboard');
    }

    public function delete($id)
    {
        $branchId = Auth::getCurrentBranch();
        // Check if exists
        $product = $this->db->query("SELECT id FROM products WHERE id = ? AND branch_id = ?", [$id, $branchId])->fetch();
        
        if ($product) {
            // Soft Delete
            $this->db->query("UPDATE products SET deleted_at = NOW() WHERE id = ?", [$id]);
        }
        
        $this->redirect('/products?success=Product moved to trash');
    }

    public function create()
    {
        $tax_groups = $this->db->query("SELECT * FROM tax_groups")->fetchAll();
        return $this->view('products/create', ['tax_groups' => $tax_groups], 'dashboard');
    }

    public function store()
    {
        // TODO: add validation for SKU uniqueness
        
        $name = $_POST['name'];
        $sku = $_POST['sku'] ?: null; // Allow NULL if empty
        $hsn = $_POST['hsn_code'];
        $unit = $_POST['unit'];
        $tax_id = $_POST['tax_group_id'];
        $alert_qty = $_POST['min_stock_alert'];

        // Check SKU Duplicate if provided (within same branch)
        if ($sku) {
            $branchId = Auth::getCurrentBranch();
            $check = $this->db->query("SELECT id FROM products WHERE sku = ? AND branch_id = ?", [$sku, $branchId]);
            if ($check->rowCount() > 0) {
                die("Error: Product with this SKU/Barcode already exists in this branch.");
            }
        }

        $branchId = Auth::getCurrentBranch();
        $sql = "INSERT INTO products (name, sku, hsn_code, unit, tax_group_id, min_stock_alert, is_active, branch_id) 
                VALUES (?, ?, ?, ?, ?, ?, 1, ?)";
        
        $this->db->query($sql, [$name, $sku, $hsn, $unit, $tax_id, $alert_qty, $branchId]);

        $this->redirect('/products');
    }
    public function edit($id)
    {
        $branchId = Auth::getCurrentBranch();
        $product = $this->db->query("SELECT * FROM products WHERE id = ? AND branch_id = ?", [$id, $branchId])->fetch();
        
        if (!$product) die("Product not found");

        $tax_groups = $this->db->query("SELECT * FROM tax_groups")->fetchAll();
        return $this->view('products/edit', [
            'product' => $product,
            'tax_groups' => $tax_groups
        ], 'dashboard');
    }

    public function update($id)
    {
        $branchId = Auth::getCurrentBranch();
        $name = $_POST['name'];
        $sku = $_POST['sku'] ?: null;
        $hsn = $_POST['hsn_code'];
        $unit = $_POST['unit'];
        $tax_id = $_POST['tax_group_id'];
        $version = (int) $_POST['version_id'];

        // Optimistic Locking Check
        $updateSql = "
            UPDATE products 
            SET name = ?, sku = ?, hsn_code = ?, unit = ?, tax_group_id = ?, version_id = version_id + 1
            WHERE id = ? AND branch_id = ? AND version_id = ?
        ";
        
        $this->db->query($updateSql, [$name, $sku, $hsn, $unit, $tax_id, $id, $branchId, $version]);

        if ($this->db->query("SELECT ROW_COUNT() as count")->fetch()['count'] == 0) {
            die("Conflict Error: The product was updated by another user. Please refresh and try again.");
        }

        $this->redirect('/products?success=Product updated');
    }

    public function editSettings($id)
    {
        $branchId = Auth::getCurrentBranch();
        $product = $this->db->query("SELECT * FROM products WHERE id = ? AND branch_id = ?", [$id, $branchId])->fetch();
        
        if (!$product) die("Product not found");

        $settings = $this->db->query("
            SELECT * FROM branch_product_settings 
            WHERE branch_id = ? AND product_id = ?
        ", [$branchId, $id])->fetch();

        return $this->view('products/settings', [
            'product' => $product,
            'settings' => $settings
        ], 'dashboard');
    }

    public function updateSettings($id)
    {
        $branchId = Auth::getCurrentBranch();
        $minStock = $_POST['min_stock_alert'];
        $reorderLevel = $_POST['reorder_level'];
        $defaultPrice = $_POST['default_sale_price'] ?: null;

        $check = $this->db->query("SELECT id FROM branch_product_settings WHERE branch_id = ? AND product_id = ?", [$branchId, $id])->fetch();

        if ($check) {
            $this->db->query("
                UPDATE branch_product_settings 
                SET min_stock_alert = ?, reorder_level = ?, default_sale_price = ?
                WHERE id = ?
            ", [$minStock, $reorderLevel, $defaultPrice, $check['id']]);
        } else {
            $this->db->query("
                INSERT INTO branch_product_settings (branch_id, product_id, min_stock_alert, reorder_level, default_sale_price)
                VALUES (?, ?, ?, ?, ?)
            ", [$branchId, $id, $minStock, $reorderLevel, $defaultPrice]);
        }

        $this->redirect('/products?success=Settings updated');
    }

    public function importCsv()
    {
        $this->requireRole(1); // Admin Only
        $file = $_FILES['csv_file']['tmp_name'] ?? null;
        if (!$file) die("No file uploaded");

        $handle = fopen($file, "r");
        fgetcsv($handle); // Skip header
        
        $branchId = Auth::getCurrentBranch();
        $imported = 0;
        
        $this->db->transactional(function($db) use ($handle, $branchId, &$imported) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) < 5) continue;
                // name, sku, hsn, unit, tax_id, alert_qty
                $db->query(
                    "INSERT INTO products (name, sku, hsn_code, unit, tax_group_id, min_stock_alert, branch_id) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$data[0], $data[1], $data[2], $data[3], $data[4], $data[5] ?? 10, $branchId]
                );
                $imported++;
            }
        });

        fclose($handle);
        $this->redirect("/products?success=Imported $imported products successfully");
    }
}
