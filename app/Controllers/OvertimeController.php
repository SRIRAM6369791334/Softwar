<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class OvertimeController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole(1); // Admin only
    }

    public function index()
    {
        $records = $this->db->query("
            SELECT o.*, u.full_name, u.username 
            FROM overtime_records o
            JOIN users u ON o.user_id = u.id
            WHERE o.status = 'pending'
            ORDER BY o.created_at DESC
        ")->fetchAll();

        return $this->view('admin/employee/overtime', ['records' => $records], 'dashboard');
    }

    public function approve($id)
    {
        $userId = $_SESSION['user_id'];
        
        $this->db->query(
            "UPDATE overtime_records SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?",
            [$userId, $id]
        );

        // Update attendance record
        $record = $this->db->query("SELECT attendance_id FROM overtime_records WHERE id = ?", [$id])->fetch();
        if ($record && $record['attendance_id']) {
            $this->db->query("UPDATE attendance_logs SET overtime_approved = 1 WHERE id = ?", [$record['attendance_id']]);
        }

        $this->redirect('/admin/employee/overtime?success=Overtime Approved');
    }

    public function reject($id)
    {
        $userId = $_SESSION['user_id'];
        
        $this->db->query(
            "UPDATE overtime_records SET status = 'rejected', approved_by = ?, approved_at = NOW() WHERE id = ?",
            [$userId, $id]
        );

        $this->redirect('/admin/employee/overtime?success=Overtime Rejected');
    }

    public function report()
    {
        $month = $_GET['month'] ?? date('Y-m');
        
        $records = $this->db->query("
            SELECT u.full_name, 
                   SUM(o.overtime_hours) as total_hours,
                   SUM(CASE WHEN o.status = 'approved' THEN o.overtime_hours ELSE 0 END) as approved_hours,
                   COUNT(*) as total_requests
            FROM overtime_records o
            JOIN users u ON o.user_id = u.id
            WHERE DATE_FORMAT(o.date, '%Y-%m') = ?
            GROUP BY o.user_id
            ORDER BY total_hours DESC
        ", [$month])->fetchAll();

        return $this->view('admin/employee/overtime-report', [
            'records' => $records,
            'month' => $month
        ], 'dashboard');
    }
}
