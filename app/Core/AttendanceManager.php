<?php

namespace App\Core;

use App\Core\Database;

class AttendanceManager
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Process clock-in with grace period logic
     */
    public function clockIn($userId, $shiftId = null)
    {
        $now = new \DateTime();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // Security: Guard against future-dated entries
        if ($now > (new \DateTime())->modify('+1 minute')) {
             throw new \Exception("System clock error or future-dated entry detected.");
        }

        // Check if already clocked in (any active session, regardless of date, though usually same day)
        $existing = $this->db->query(
            "SELECT id FROM attendance_logs WHERE user_id = ? AND clock_out IS NULL",
            [$userId]
        )->fetch();

        if ($existing) {
            return ['success' => false, 'message' => 'Active session already exists. Please clock out first.'];
        }

        // Get user's grace period settings
        $user = $this->db->query("SELECT grace_period_minutes, max_grace_uses_per_month FROM users WHERE id = ?", [$userId])->fetch();
        $graceLimit = $user['max_grace_uses_per_month'] ?? 3;
        $gracePeriod = $user['grace_period_minutes'] ?? 15;

        // Check monthly grace usage
        $currentUsage = $this->getMonthlyGraceUsage($userId);

        // Get shift details if provided
        $scheduledTime = null;
        $graceUsed = 0;
        $graceMinutes = 0;

        if ($shiftId) {
            $shift = $this->db->query("SELECT start_time FROM employee_shifts WHERE id = ?", [$shiftId])->fetch();
            if ($shift) {
                $scheduledTime = new \DateTime($shift['start_time']);
                $diff = ($now->getTimestamp() - $scheduledTime->getTimestamp()) / 60; // minutes

                if ($diff > 0 && $diff <= $gracePeriod) {
                    // Within grace period - CHECK IF CAP EXCEEDED
                    if ($currentUsage >= $graceLimit) {
                        // Cap exceeded - mark as late without grace
                        $graceUsed = 0;
                        $graceMinutes = (int)$diff;
                    } else {
                        $graceUsed = 1;
                        $graceMinutes = (int)$diff;

                        // Log grace usage
                        $this->db->query(
                            "INSERT INTO grace_period_logs (user_id, scheduled_time, actual_time, grace_minutes, date) VALUES (?, ?, ?, ?, ?)",
                            [$userId, $scheduledTime->format('H:i:s'), $time, $graceMinutes, $date]
                        );
                    }
                } elseif ($diff > $gracePeriod) {
                    // Beyond grace period
                    $graceMinutes = (int)$diff;
                }
            }
        }

        // Insert attendance record
        $this->db->query(
            "INSERT INTO attendance_logs (user_id, clock_in, date, grace_minutes, status) VALUES (?, ?, ?, ?, ?)",
            [$userId, $now->format('Y-m-d H:i:s'), $date, $graceMinutes, $graceUsed ? 'late' : 'present']
        );

        return [
            'success' => true,
            'message' => $graceUsed ? "Clocked in (Grace: {$graceMinutes} min)" : 'Clocked in successfully',
            'grace_used' => $graceUsed
        ];
    }

    /**
     * Process clock-out with overtime detection
     */
    public function clockOut($userId)
    {
        $now = new \DateTime();
        $date = $now->format('Y-m-d');

        // Find active attendance
        $attendance = $this->db->query(
            "SELECT * FROM attendance_logs WHERE user_id = ? AND date = ? AND clock_out IS NULL",
            [$userId, $date]
        )->fetch();

        if (!$attendance) {
            return ['success' => false, 'message' => 'No active clock-in found'];
        }

        $clockIn = new \DateTime($attendance['clock_in']);
        $totalHours = ($now->getTimestamp() - $clockIn->getTimestamp()) / 3600;

        // Update attendance
        $this->db->query(
            "UPDATE attendance_logs SET clock_out = ?, total_hours = ? WHERE id = ?",
            [$now->format('Y-m-d H:i:s'), round($totalHours, 2), $attendance['id']]
        );

        // Check for overtime (assuming 8-hour standard shift)
        $overtimeHours = max(0, $totalHours - 8);
        
        if ($overtimeHours > 0) {
            // Create overtime record
            $this->db->query(
                "INSERT INTO overtime_records (user_id, attendance_id, date, overtime_hours, status) VALUES (?, ?, ?, ?, 'pending')",
                [$userId, $attendance['id'], $date, round($overtimeHours, 2)]
            );

            return [
                'success' => true,
                'message' => sprintf('Clocked out. Overtime: %.2f hrs (pending approval)', $overtimeHours),
                'overtime' => round($overtimeHours, 2)
            ];
        }

        return ['success' => true, 'message' => 'Clocked out successfully'];
    }

    /**
     * Get grace period usage for current month
     */
    public function getMonthlyGraceUsage($userId)
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        $result = $this->db->query(
            "SELECT COUNT(*) as count FROM grace_period_logs WHERE user_id = ? AND date BETWEEN ? AND ?",
            [$userId, $startOfMonth, $endOfMonth]
        )->fetch();

        return $result['count'] ?? 0;
    }
}
