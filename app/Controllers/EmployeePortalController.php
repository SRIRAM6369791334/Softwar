<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class EmployeePortalController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function login()
    {
        if (Auth::check()) {
            $this->redirect('/employee/dashboard');
        }
        return $this->view('employee/login', [], 'auth'); 
    }

    public function authenticate()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->db->query("SELECT * FROM users WHERE username = ? AND status = 'active'", [$username])->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            Auth::login($user);
            $this->redirect('/employee/dashboard');
        } else {
            return $this->view('employee/login', ['error' => 'Invalid credentials'], 'auth');
        }
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/employee/login');
    }

    public function dashboard()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        $userId = Auth::id();
        $date = date('Y-m-d');

        // Check Attendance Status
        $todayLog = $this->db->query("SELECT * FROM attendance_logs WHERE user_id = ? AND date = ?", [$userId, $date])->fetch();
        
        // Upcoming Shift
        $shift = $this->db->query("SELECT * FROM employee_shifts WHERE user_id = ? AND start_time > NOW() ORDER BY start_time ASC LIMIT 1", [$userId])->fetch();

        return $this->view('employee/dashboard', [
            'todayLog' => $todayLog,
            'nextShift' => $shift,
            'active_tab' => 'dashboard'
        ], 'employee_portal');
    }

    public function clockIn()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        $userId = Auth::id();
        $date = date('Y-m-d');

        // Prevent double clock-in
        $exists = $this->db->query("SELECT id FROM attendance_logs WHERE user_id = ? AND date = ?", [$userId, $date])->fetch();
        if ($exists) {
            $this->redirect('/employee/dashboard?error=Already clocked in today');
            return;
        }

        // --- Geofence Validation ---
        $lat = $_GET['lat'] ?? null;
        $lon = $_GET['lon'] ?? null;

        if (!$lat || !$lon) {
            $this->redirect('/employee/dashboard?error=Location access required for clock-in');
            return;
        }

        // Get User's Branch Location
        $user = $this->db->query("
            SELECT u.branch_id, b.latitude, b.longitude, b.geofence_radius 
            FROM users u 
            JOIN branches b ON u.branch_id = b.id 
            WHERE u.id = ?
        ", [$userId])->fetch();

        if ($user['latitude'] && $user['longitude']) {
            $distance = $this->calculateDistance($lat, $lon, $user['latitude'], $user['longitude']);
            if ($distance > $user['geofence_radius']) {
                $this->redirect("/employee/dashboard?error=You are off-site! ($distance m away)");
                return;
            }
        }
        // ---------------------------

        // Check Grace Period
        $gracePeriod = 15; // Default
        $setting = $this->db->query("SELECT value FROM settings WHERE `key` = 'attendance_grace_period'")->fetch();
        if ($setting) $gracePeriod = (int)$setting['value'];

        $status = 'present';
        
        // Find if there is a shift starting around now
        $shift = $this->db->query("
            SELECT * FROM employee_shifts 
            WHERE user_id = ? 
            AND start_time BETWEEN DATE_SUB(NOW(), INTERVAL 1 HOUR) AND DATE_ADD(NOW(), INTERVAL 4 HOUR)
            LIMIT 1
        ", [$userId])->fetch();

        if ($shift) {
            $startTime = strtotime($shift['start_time']);
            $currentTime = time();
            $diffMinutes = ($currentTime - $startTime) / 60;

            if ($diffMinutes > $gracePeriod) {
                $status = 'late';
            }
        }

        $this->db->query("
            INSERT INTO attendance_logs (user_id, clock_in, date, status) 
            VALUES (?, NOW(), ?, ?)
        ", [$userId, $date, $status]);

        $msg = "Clocked In Successfully";
        if ($status == 'late') $msg .= " (Marked as Late)";

        $this->redirect("/employee/dashboard?success=$msg");
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return round($earthRadius * $c);
    }

    public function clockOut()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        $userId = Auth::id();
        $date = date('Y-m-d');

        // Calculate Overtime
        $log = $this->db->query("SELECT * FROM attendance_logs WHERE user_id = ? AND date = ?", [$userId, $date])->fetch();
        
        $clockInTime = strtotime($log['clock_in']);
        $clockOutTime = time();
        $totalHours = ($clockOutTime - $clockInTime) / 3600;

        // Check assigned shift duration
        $shift = $this->db->query("
            SELECT TIMESTAMPDIFF(HOUR, start_time, end_time) as duration 
            FROM employee_shifts 
            WHERE user_id = ? AND DATE(start_time) = ?
        ", [$userId, $date])->fetch();

        $isOvertime = 0;
        $overtimeMinutes = 0;

        if ($shift) {
            $scheduledHours = $shift['duration'];
            if ($totalHours > $scheduledHours) {
                $isOvertime = 1;
                $overtimeMinutes = ($totalHours - $scheduledHours) * 60;
            }
        }

        $this->db->query("
            UPDATE attendance_logs 
            SET clock_out = NOW(), 
                total_hours = ?,
                is_overtime = ?,
                overtime_minutes = ?
            WHERE user_id = ? AND date = ? AND clock_out IS NULL
        ", [$totalHours, $isOvertime, $overtimeMinutes, $userId, $date]);

        $this->redirect('/employee/dashboard?success=Clocked Out');
    }

    public function roster()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        $userId = Auth::id();
        
        // Get shifts for this week
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

        // Assigned Shifts
        $shifts = $this->db->query("
            SELECT * FROM employee_shifts 
            WHERE user_id = ? AND start_time BETWEEN ? AND ?
            ORDER BY start_time ASC
        ", [$userId, $startOfWeek, $endOfWeek])->fetchAll();

        // Open Shifts
        $openShifts = $this->db->query("
             SELECT * FROM employee_shifts 
             WHERE is_open = 1 
             AND start_time >= NOW()
             AND (user_id IS NULL OR user_id = 0)
             ORDER BY start_time ASC
        ")->fetchAll();

        return $this->view('employee/roster', ['shifts' => $shifts, 'openShifts' => $openShifts], 'employee_portal');
    }

    public function claimShift($id)
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        $userId = Auth::id();

        // Verify shift is open
        $shift = $this->db->query("SELECT * FROM employee_shifts WHERE id = ? AND is_open = 1 AND (user_id IS NULL OR user_id = 0)", [$id])->fetch();

        if ($shift) {
            $this->db->query("UPDATE employee_shifts SET user_id = ?, is_open = 0 WHERE id = ?", [$userId, $id]);
            $this->redirect('/employee/roster?success=Shift Claimed! It is now on your roster.');
        } else {
            $this->redirect('/employee/roster?error=Shift unavailable');
        }
    }

    public function messages()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        
        // Get messages for user or their role
        // For simplicity, showing all messages for now
        $messages = $this->db->query("
            SELECT em.*, u.full_name as sender 
            FROM employee_messages em
            JOIN users u ON em.sender_id = u.id
            ORDER BY em.created_at DESC
        ")->fetchAll();

        return $this->view('employee/messages', ['messages' => $messages], 'employee_portal');
    }

    public function leaves()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        $userId = Auth::id();

        $leaves = $this->db->query("SELECT * FROM employee_leaves WHERE user_id = ? ORDER BY created_at DESC", [$userId])->fetchAll();

        return $this->view('employee/leaves', ['leaves' => $leaves], 'employee_portal');
    }

    public function requestLeave()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        $userId = Auth::id();

        $type = $_POST['type'];
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $reason = $_POST['reason'];
        
        // Calc days (rough)
        $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;

        $this->db->query("
            INSERT INTO employee_leaves (user_id, type, start_date, end_date, days, reason, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ", [$userId, $type, $startDate, $endDate, $days, $reason]);

        $this->redirect('/employee/leaves?success=Request Submitted');
    }
}
