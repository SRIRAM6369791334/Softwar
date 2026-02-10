<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class LogController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole(1); // Admin Only [#63]
    }

    public function index()
    {
        $logs = $this->db->query("
            SELECT * FROM admin_actions 
            ORDER BY created_at DESC 
            LIMIT 500
        ")->fetchAll();

        return $this->view('admin/logs/index', ['logs' => $logs], 'dashboard');
    }

    public function dashboard()
    {
        // Aggregate Stats
        $stats = [
            'total' => $this->db->query("SELECT COUNT(*) as c FROM admin_actions")->fetch()['c'],
            'risks' => $this->db->query("SELECT COUNT(*) as c FROM admin_actions WHERE action LIKE 'RISK_%' OR action = 'SLOW_QUERY'")->fetch()['c'],
            'errors' => $this->db->query("SELECT COUNT(*) as c FROM automation_logs WHERE status = 'error'")->fetch()['c']
        ];

        return $this->view('admin/logs/dashboard', ['stats' => $stats], 'dashboard');
    }
}
