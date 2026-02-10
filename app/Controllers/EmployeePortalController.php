<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class EmployeePortalController extends Controller
{
    private $db;

    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function getAttendanceCsrfToken(): string
    {
        $this->ensureSessionStarted();

        if (empty($_SESSION['attendance_csrf_token'])) {
            $_SESSION['attendance_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['attendance_csrf_token'];
    }

    private function validateAttendanceCsrfToken(?string $token): bool
    {
        $this->ensureSessionStarted();

        if (!is_string($token) || $token === '') {
            return false;
        }

        return hash_equals((string)($_SESSION['attendance_csrf_token'] ?? ''), $token);
    }

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    private function sanitizeLocation($value): ?float
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function saveAttendanceSelfie(int $userId, string $mode, ?string $dataUrl): ?string
    {
        if (!$dataUrl || !preg_match('/^data:image\/(png|jpe?g);base64,/', $dataUrl, $matches)) {
            return null;
        }

        $base64 = preg_replace('/^data:image\/(png|jpe?g);base64,/', '', $dataUrl);
        $binary = base64_decode($base64, true);
        if ($binary === false) {
            return null;
        }

        // ~3MB max raw payload (defensive check)
        if (strlen($binary) > 3 * 1024 * 1024) {
            return null;
        }

        $imageInfo = @getimagesizefromstring($binary);
        if (!$imageInfo || !in_array(($imageInfo['mime'] ?? ''), ['image/jpeg', 'image/png'], true)) {
            return null;
        }

        $ext = strtolower($matches[1]) === 'png' ? 'png' : 'jpg';
        $dir = APP_ROOT . '/public/uploads/attendance';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = sprintf(
            'u%d_%s_%s_%s.%s',
            $userId,
            $mode,
            date('Ymd_His'),
            bin2hex(random_bytes(5)),
            $ext
        );

        $path = $dir . '/' . $filename;
        if (file_put_contents($path, $binary) === false) {
            return null;
        }

        return $filename;
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

        $todayLog = $this->db->query("SELECT * FROM attendance_logs WHERE user_id = ? AND date = ?", [$userId, $date])->fetch();
        $shift = $this->db->query("SELECT * FROM employee_shifts WHERE user_id = ? AND start_time > NOW() ORDER BY start_time ASC LIMIT 1", [$userId])->fetch();

        return $this->view('employee/dashboard', [
            'todayLog' => $todayLog,
            'nextShift' => $shift,
            'attendanceCsrfToken' => $this->getAttendanceCsrfToken(),
            'active_tab' => 'dashboard'
        ], 'employee_portal');
    }

    public function clockIn()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/employee/dashboard?error=Use the clock-in button to submit attendance.');
            return;
        }

        if (!$this->validateAttendanceCsrfToken($_POST['attendance_csrf_token'] ?? null)) {
            $this->redirect('/employee/dashboard?error=Invalid attendance session token. Please refresh and try again.');
            return;
        }

        $userId = Auth::id();
        $date = date('Y-m-d');

        $exists = $this->db->query("SELECT id FROM attendance_logs WHERE user_id = ? AND date = ?", [$userId, $date])->fetch();
        if ($exists) {
            $this->redirect('/employee/dashboard?error=Already clocked in today');
            return;
        }

        $lat = $this->sanitizeLocation($_POST['lat'] ?? null);
        $lon = $this->sanitizeLocation($_POST['lon'] ?? null);

        if ($lat === null || $lon === null || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            $this->redirect('/employee/dashboard?error=Valid location is required for clock-in');
            return;
        }

        $user = $this->db->query("
            SELECT u.branch_id, b.latitude, b.longitude, b.geofence_radius
            FROM users u
            JOIN branches b ON u.branch_id = b.id
            WHERE u.id = ?
        ", [$userId])->fetch();

        if ($user && $user['latitude'] && $user['longitude']) {
            $distance = $this->calculateDistance($lat, $lon, $user['latitude'], $user['longitude']);
            if ($distance > (float) ($user['geofence_radius'] ?? 0)) {
                $this->redirect('/employee/dashboard?error=' . urlencode("You are off-site! ($distance m away)"));
                return;
            }
        }

        $gracePeriod = 15;
        $setting = $this->db->query("SELECT value FROM settings WHERE `key` = 'attendance_grace_period'")->fetch();
        if ($setting) $gracePeriod = (int) $setting['value'];

        $status = 'present';
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

        $clockInPhoto = $this->saveAttendanceSelfie($userId, 'in', $_POST['photo_data'] ?? null);
        if (!$clockInPhoto) {
            $this->redirect('/employee/dashboard?error=Selfie capture is required for clock-in');
            return;
        }

        $this->db->query("
            INSERT INTO attendance_logs (user_id, clock_in, date, status, clock_in_photo, clock_in_latitude, clock_in_longitude)
            VALUES (?, NOW(), ?, ?, ?, ?, ?)
        ", [$userId, $date, $status, $clockInPhoto, $lat, $lon]);

        $msg = 'Clocked In Successfully';
        if ($status === 'late') $msg .= ' (Marked as Late)';
        if (!$clockInPhoto) $msg .= ' (No selfie captured)';

        $this->redirect('/employee/dashboard?success=' . urlencode($msg));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c);
    }

    public function clockOut()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/employee/dashboard?error=Use the clock-out button to submit attendance.');
            return;
        }

        if (!$this->validateAttendanceCsrfToken($_POST['attendance_csrf_token'] ?? null)) {
            $this->redirect('/employee/dashboard?error=Invalid attendance session token. Please refresh and try again.');
            return;
        }

        $userId = Auth::id();
        $date = date('Y-m-d');

        $log = $this->db->query("SELECT * FROM attendance_logs WHERE user_id = ? AND date = ?", [$userId, $date])->fetch();
        if (!$log || empty($log['clock_in'])) {
            $this->redirect('/employee/dashboard?error=No active attendance found for today');
            return;
        }

        if (!empty($log['clock_out'])) {
            $this->redirect('/employee/dashboard?error=Already clocked out for today');
            return;
        }

        $clockInTime = strtotime($log['clock_in']);
        $clockOutTime = time();
        $totalHours = ($clockOutTime - $clockInTime) / 3600;

        $shift = $this->db->query("
            SELECT TIMESTAMPDIFF(HOUR, start_time, end_time) as duration
            FROM employee_shifts
            WHERE user_id = ? AND DATE(start_time) = ?
        ", [$userId, $date])->fetch();

        $isOvertime = 0;
        $overtimeMinutes = 0;

        if ($shift) {
            $scheduledHours = (float) $shift['duration'];
            if ($totalHours > $scheduledHours) {
                $isOvertime = 1;
                $overtimeMinutes = ($totalHours - $scheduledHours) * 60;
            }
        }

        $lat = $this->sanitizeLocation($_POST['lat'] ?? null);
        $lon = $this->sanitizeLocation($_POST['lon'] ?? null);
        if ($lat === null || $lon === null || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            $this->redirect('/employee/dashboard?error=Valid location is required for clock-out');
            return;
        }

        $clockOutPhoto = $this->saveAttendanceSelfie($userId, 'out', $_POST['photo_data'] ?? null);
        if (!$clockOutPhoto) {
            $this->redirect('/employee/dashboard?error=Selfie capture is required for clock-out');
            return;
        }

        $this->db->query("
            UPDATE attendance_logs
            SET clock_out = NOW(),
                total_hours = ?,
                is_overtime = ?,
                overtime_minutes = ?,
                clock_out_photo = ?,
                clock_out_latitude = ?,
                clock_out_longitude = ?
            WHERE user_id = ? AND date = ? AND clock_out IS NULL
        ", [$totalHours, $isOvertime, $overtimeMinutes, $clockOutPhoto, $lat, $lon, $userId, $date]);

        $this->redirect('/employee/dashboard?success=' . urlencode('Clocked Out'));
    }

    public function roster()
    {
        if (!Auth::check()) $this->redirect('/employee/login');
        $userId = Auth::id();

        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

        $shifts = $this->db->query("
            SELECT * FROM employee_shifts
            WHERE user_id = ? AND start_time BETWEEN ? AND ?
            ORDER BY start_time ASC
        ", [$userId, $startOfWeek, $endOfWeek])->fetchAll();

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

        $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;

        $this->db->query("
            INSERT INTO employee_leaves (user_id, type, start_date, end_date, days, reason, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ", [$userId, $type, $startDate, $endDate, $days, $reason]);

        $this->redirect('/employee/leaves?success=Request Submitted');
    }
}
