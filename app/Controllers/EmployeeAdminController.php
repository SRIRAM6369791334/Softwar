<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class EmployeeAdminController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function roster()
    {
        $users = $this->db->query("SELECT id, full_name, role_id FROM users WHERE status = 'active'")->fetchAll();
        
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

        $shifts = $this->db->query("
            SELECT es.*, u.full_name 
            FROM employee_shifts es
            JOIN users u ON es.user_id = u.id
            WHERE start_time BETWEEN ? AND ?
        ", [$startOfWeek, $endOfWeek])->fetchAll();

        return $this->view('admin/employee/roster', [
            'users' => $users,
            'shifts' => $shifts,
            'weekStart' => $startOfWeek
        ], 'dashboard');
    }

    public function saveShift()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $userId = $data['user_id'];
        $startTime = $data['start_time']; // '2023-10-27 09:00:00'
        $endTime = $data['end_time'];
        $notes = $data['notes'] ?? '';

        $this->db->query("
            INSERT INTO employee_shifts (user_id, start_time, end_time, notes)
            VALUES (?, ?, ?, ?)
        ", [$userId, $startTime, $endTime, $notes]);

        $this->json(['success' => true, 'id' => $this->db->getConnection()->lastInsertId()]);
    }

    public function deleteShift($id)
    {
        $this->db->query("DELETE FROM employee_shifts WHERE id = ?", [$id]);
        $this->json(['success' => true]);
    }

    public function leaves()
    {
        $leaves = $this->db->query("
            SELECT el.*, u.full_name, u.username
            FROM employee_leaves el
            JOIN users u ON el.user_id = u.id
            ORDER BY FIELD(el.status, 'pending', 'approved', 'rejected'), el.created_at DESC
        ")->fetchAll();

        return $this->view('admin/employee/leaves', ['leaves' => $leaves], 'dashboard');
    }

    public function updateLeaveStatus($id)
    {
        $status = $_POST['status']; // approved / rejected
        $this->db->query("
            UPDATE employee_leaves 
            SET status = ?, approved_by = ? 
            WHERE id = ?
        ", [$status, Auth::id(), $id]);

        $this->redirect('/admin/employee/leaves?success=Status Updated');
    }

    public function messages()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $message = $_POST['message'];
            $isUrgent = isset($_POST['is_urgent']) ? 1 : 0;

            $this->db->query("
                INSERT INTO employee_messages (sender_id, title, message, is_urgent)
                VALUES (?, ?, ?, ?)
            ", [Auth::id(), $title, $message, $isUrgent]);

            $this->redirect('/admin/employee/messages?success=Message Sent');
        }

        $history = $this->db->query("SELECT * FROM employee_messages ORDER BY created_at DESC LIMIT 10")->fetchAll();
        return $this->view('admin/employee/messages', ['history' => $history], 'dashboard');
    }

    public function timesheets()
    {
        $startOfWeek = $_GET['week'] ?? date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime($startOfWeek . ' +6 days'));

        $logs = $this->db->query("
            SELECT al.*, u.full_name, u.username
            FROM attendance_logs al
            JOIN users u ON al.user_id = u.id
            WHERE al.date BETWEEN ? AND ?
            ORDER BY u.full_name, al.date
        ", [$startOfWeek, $endOfWeek])->fetchAll();

        // Group by User
        $report = [];
        foreach ($logs as $log) {
            $report[$log['user_id']]['name'] = $log['full_name'];
            $report[$log['user_id']]['logs'][] = $log;
            if (!isset($report[$log['user_id']]['total_hours'])) $report[$log['user_id']]['total_hours'] = 0;
            $report[$log['user_id']]['total_hours'] += $log['total_hours'];
            
            if (!isset($report[$log['user_id']]['overtime_minutes'])) $report[$log['user_id']]['overtime_minutes'] = 0;
            $report[$log['user_id']]['overtime_minutes'] += $log['overtime_minutes'];
        }

        return $this->view('admin/employee/timesheets', ['report' => $report, 'week' => $startOfWeek], 'dashboard');
    }
}
