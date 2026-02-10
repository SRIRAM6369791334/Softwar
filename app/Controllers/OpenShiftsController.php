<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class OpenShiftsController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Admin: List all open shifts
    public function index()
    {
        $this->requireRole(1);
        
        $shifts = $this->db->query("
            SELECT s.*, u.full_name as claimed_by_name
            FROM employee_shifts s
            LEFT JOIN users u ON s.claimed_by = u.id
            WHERE s.is_open = 1
            ORDER BY s.start_time ASC
        ")->fetchAll();

        return $this->view('admin/employee/open-shifts', ['shifts' => $shifts], 'dashboard');
    }

    // Admin: Create open shift
    public function create()
    {
        $this->requireRole(1);
        return $this->view('admin/employee/open-shifts-create', [], 'dashboard');
    }

    public function store()
    {
        $this->requireRole(1);

        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $type = $_POST['type'];
        $notes = $_POST['notes'] ?? '';

        $this->db->query(
            "INSERT INTO employee_shifts (user_id, start_time, end_time, type, notes, is_open) VALUES (0, ?, ?, ?, ?, 1)",
            [$startTime, $endTime, $type, $notes]
        );

        $this->redirect('/admin/employee/open-shifts?success=Open Shift Created');
    }

    // Employee: Browse available shifts
    public function browse()
    {
        $shifts = $this->db->query("
            SELECT * FROM employee_shifts 
            WHERE is_open = 1 AND claimed_by IS NULL
            AND start_time > NOW()
            ORDER BY start_time ASC
        ")->fetchAll();

        return $this->view('employee/open-shifts', ['shifts' => $shifts], 'dashboard');
    }

    // Employee: Claim a shift
    public function claim($id)
    {
        $userId = $_SESSION['user_id'];

        // Check if shift is still available
        $shift = $this->db->query("SELECT * FROM employee_shifts WHERE id = ? AND is_open = 1 AND claimed_by IS NULL", [$id])->fetch();
        
        if (!$shift) {
            $this->redirect('/employee/open-shifts?error=Shift no longer available');
            return;
        }

        // Claim the shift
        $this->db->query(
            "UPDATE employee_shifts SET claimed_by = ?, claimed_at = NOW(), user_id = ? WHERE id = ?",
            [$userId, $userId, $id]
        );

        // Trigger automation
        \App\Core\Automation::trigger('shift_claimed', [
            'user_id' => $userId,
            'shift_id' => $id,
            'shift_time' => $shift['start_time']
        ]);

        $this->redirect('/employee/open-shifts?success=Shift Claimed Successfully');
    }
}
