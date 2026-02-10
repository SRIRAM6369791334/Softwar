<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class UserController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole([1, 2, 3]); // Allow all authenticated users
    }

    public function profile()
    {
        $userId = \App\Core\Auth::id();
        $user = $this->db->query("SELECT * FROM users WHERE id = ?", [$userId])->fetch();
        $devices = $this->db->query("SELECT * FROM user_biometrics WHERE user_id = ? ORDER BY created_at DESC", [$userId])->fetchAll();

        return $this->view('users/profile', ['user' => $user, 'devices' => $devices], 'dashboard');
    }

    public function index()
    {
        $this->requireRole(1); // Admin Only index
        // Simple Listing
        $stmt = $this->db->query("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            ORDER BY u.id ASC
        ");
        $users = $stmt->fetchAll();
        
        return $this->view('users/index', ['users' => $users], 'dashboard');
    }

    public function create()
    {
        $roles = $this->db->query("SELECT * FROM roles")->fetchAll();
        $branches = $this->db->query("SELECT * FROM branches WHERE is_active = 1")->fetchAll();
        return $this->view('users/create', ['roles' => $roles, 'branches' => $branches], 'dashboard');
    }

    public function store()
    {
        // Basic Validation
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role_id = $_POST['role_id'];
        $branch_id = $_POST['branch_id'];
        $full_name = $_POST['full_name'];

        // Check if username exists
        $check = $this->db->query("SELECT id FROM users WHERE username = ?", [$username]);
        if ($check->rowCount() > 0) {
            die("Error: Username already exists.");
        }

        $email = $_POST['email'] ?? null;
        $phone = $_POST['phone'] ?? null;

        $sql = "INSERT INTO users (username, password_hash, role_id, branch_id, full_name, email, phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";
        $this->db->query($sql, [$username, $password, $role_id, $branch_id, $full_name, $email, $phone]);

        // Trigger Automation Event (Phase 25)
        \App\Core\Automation::trigger('user_created', [
            'full_name' => $full_name,
            'email' => $email,
            'username' => $username,
            'password_plain' => $_POST['password'] ?? '********'
        ]);

        $this->redirect('/users?success=Employee Created');
    }
}
