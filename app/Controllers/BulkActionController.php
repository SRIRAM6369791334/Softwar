<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class BulkActionController extends Controller
{
    private $db;

    public function __construct()
    {
        // Admin Only
        if (!Auth::check() || Auth::user()['role_id'] != 1) {
            header('Location: /login');
            exit;
        }
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $categories = $this->db->query("SELECT * FROM categories")->fetchAll();
        $branches = $this->db->query("SELECT * FROM branches WHERE is_active = 1")->fetchAll();
        
        return $this->view('admin/data/index', [
            'categories' => $categories, 
            'branches' => $branches
        ]);
    }

    public function updatePrices()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = $_POST['category_id'];
            $percentage = floatval($_POST['percentage']);
            $type = $_POST['type']; // 'increase' or 'decrease'
            
            if ($percentage <= 0) die("Invalid percentage");

            $factor = 1 + ($percentage / 100);
            if ($type === 'decrease') {
                $factor = 1 - ($percentage / 100);
            }

            // Update Prices
            // Assuming products table has 'price' and 'category_id'
            // If checking fails, wrap in try-catch
            try {
                $sql = "UPDATE products SET price = price * ? WHERE category_id = ?";
                if ($categoryId === 'all') {
                    $sql = "UPDATE products SET price = price * ?";
                    $this->db->query($sql, [$factor]);
                } else {
                    $this->db->query($sql, [$factor, $categoryId]);
                }
                
                header('Location: /admin/data?success=Prices Updated');
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function resetInventory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $branchId = $_POST['branch_id'];
            $password = $_POST['password'];

            // verify admin password
            $user = $this->db->query("SELECT password_hash FROM users WHERE id = ?", [Auth::id()])->fetch();
            if (!password_verify($password, $user['password_hash'])) {
                header('Location: /admin/data?error=Invalid Password');
                exit;
            }

            // Reset Stock
            // Assuming 'product_batches' holds stock. Set quantity = 0? Or delete batches?
            // "Zero Out Stock" usually means setting qty to 0.
            try {
                if ($branchId === 'all') {
                    $this->db->query("UPDATE product_batches SET quantity = 0");
                } else {
                    $this->db->query("UPDATE product_batches SET quantity = 0 WHERE branch_id = ?", [$branchId]);
                }
                 header('Location: /admin/data?success=Inventory Reset Successful');
            } catch (\Exception $e) {
                 header('Location: /admin/data?error=' . urlencode($e->getMessage()));
            }
        }
    }
}
