<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class BranchController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole(1); // Admin Only
    }

    /**
     * List all branches
     */
    public function index()
    {
        // Get all branches with their stats
        $branches = $this->db->query("
            SELECT 
                b.*,
                u.full_name as manager_name,
                (SELECT COUNT(*) FROM users WHERE branch_id = b.id) as staff_count,
                (SELECT COUNT(*) FROM products WHERE branch_id = b.id) as product_count,
                (SELECT COALESCE(SUM(i.grand_total), 0) 
                 FROM invoices i 
                 WHERE i.branch_id = b.id 
                 AND DATE(i.created_at) = CURDATE() 
                 AND i.status = 'paid') as today_sales
            FROM branches b
            LEFT JOIN users u ON b.manager_id = u.id
            ORDER BY b.id ASC
        ")->fetchAll();

        return $this->view('branches/index', [
            'branches' => $branches,
            'current_branch_id' => Auth::getCurrentBranch()
        ], 'dashboard');
    }

    /**
     * Show create branch form
     */
    public function create()
    {
        // Get all managers (role_id = 1 or 2)
        $managers = $this->db->query("
            SELECT id, full_name, username 
            FROM users 
            WHERE role_id IN (1, 2)
            ORDER BY full_name
        ")->fetchAll();

        return $this->view('branches/create', [
            'managers' => $managers
        ], 'dashboard');
    }

    /**
     * Store new branch
     */
    /**
     * Store new branch
     */
    public function store()
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_SPECIAL_CHARS);
        $manager_id = filter_input(INPUT_POST, 'manager_id', FILTER_VALIDATE_INT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
        // Geofence fields
        $latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
        $longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);
        $geofence_radius = filter_input(INPUT_POST, 'geofence_radius', FILTER_VALIDATE_INT) ?: 100;

        if (empty($name)) {
            $_SESSION['error'] = "Branch name is required";
            header('Location: /branches/create');
            exit;
        }

        // Handle Background Upload
        $background_url = null;
        if (isset($_FILES['background']) && $_FILES['background']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = APP_ROOT . '/public/uploads/backgrounds/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $ext = pathinfo($_FILES['background']['name'], PATHINFO_EXTENSION);
            $filename = 'branch_' . time() . '.' . $ext;
            
            if (move_uploaded_file($_FILES['background']['tmp_name'], $uploadDir . $filename)) {
                $background_url = '/uploads/backgrounds/' . $filename;
            }
        }

        $this->db->query("
            INSERT INTO branches (name, location, manager_id, phone, background_url, latitude, longitude, geofence_radius, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
        ", [$name, $location, $manager_id, $phone, $background_url, $latitude, $longitude, $geofence_radius]);

        $_SESSION['success'] = "Branch '$name' created successfully!";
        header('Location: /branches');
        exit;
    }

    // ... (edit method unchanged) ...

    /**
     * Update branch
     */
    public function update($id)
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_SPECIAL_CHARS);
        $manager_id = filter_input(INPUT_POST, 'manager_id', FILTER_VALIDATE_INT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        // Geofence fields
        $latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
        $longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);
        $geofence_radius = filter_input(INPUT_POST, 'geofence_radius', FILTER_VALIDATE_INT) ?: 100;


        if (empty($name)) {
            $_SESSION['error'] = "Branch name is required";
            header("Location: /branches/edit/$id");
            exit;
        }

        // Handle Background Upload
        $background_sql = "";
        $params = [$name, $location, $manager_id, $phone, $is_active, $latitude, $longitude, $geofence_radius]; // Add new params

        if (isset($_FILES['background']) && $_FILES['background']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = APP_ROOT . '/public/uploads/backgrounds/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $ext = pathinfo($_FILES['background']['name'], PATHINFO_EXTENSION);
            $filename = 'branch_' . $id . '_' . time() . '.' . $ext;
            
            if (move_uploaded_file($_FILES['background']['tmp_name'], $uploadDir . $filename)) {
                $background_sql = ", background_url = ?";
                $params[] = '/uploads/backgrounds/' . $filename;
            }
        }
        
        $params[] = $id; // Add ID at the end

        // Need to update SQL query to include new columns
        // Moving $background_sql to end of SET clause to match param order logic requires care.
        // Let's rewrite the query construction to be safer.
        
        $sql = "UPDATE branches SET name=?, location=?, manager_id=?, phone=?, is_active=?, latitude=?, longitude=?, geofence_radius=?";
        if ($background_sql) $sql .= $background_sql;
        $sql .= " WHERE id=?";
        
        $this->db->query($sql, $params);

        $_SESSION['success'] = "Branch updated successfully!";
        header('Location: /branches');
        exit;
    }

    /**
     * Switch active branch (changes session context)
     */
    public function switchBranch($id)
    {
        // Verify branch exists and is active
        $branch = $this->db->query("
            SELECT * FROM branches WHERE id = ? AND is_active = 1
        ", [$id])->fetch();

        if (!$branch) {
            echo json_encode(['success' => false, 'message' => 'Invalid branch']);
            exit;
        }

        // Update session
        $_SESSION['branch_id'] = $id;
        $_SESSION['branch_name'] = $branch['name'];

        // Return JSON for AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode([
                'success' => true, 
                'branch_id' => $id,
                'branch_name' => $branch['name'],
                'message' => "Switched to {$branch['name']}"
            ]);
            exit;
        }

        // Redirect for normal requests
        header('Location: /dashboard');
        exit;
    }
}
