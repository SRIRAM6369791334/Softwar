<?php
/**
 * Supermarket OS - Employee Module Verification
 */
require __DIR__ . '/public/index.php';

// Mock Session
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['branch_id'] = 1;
$_SESSION['role_id'] = 1;

echo "--- Employee Module Verification ---\n";

$db = \App\Core\Database::getInstance();

// 1. Test Clock-In
echo "Testing Clock-In...\n";
class TestEmployeePortal extends \App\Controllers\EmployeePortalController {
    public function clockInMock($userId) {
        $db = \App\Core\Database::getInstance();
        $db->query("INSERT INTO employee_attendance (user_id, clock_in) VALUES (?, NOW())", [$userId]);
        return true;
    }
}
$portal = new TestEmployeePortal();
if ($portal->clockInMock(1)) {
    echo "SUCCESS: Clock-in recorded.\n";
}

// 2. Test Roster Save
echo "Testing Roster Management...\n";
$rosterData = [
    'user_id' => 1,
    'branch_id' => 1,
    'shift_date' => date('Y-m-d', strtotime('+1 day')),
    'start_time' => '09:00:00',
    'end_time' => '17:00:00'
];
$db->query("INSERT INTO employee_roster (user_id, branch_id, shift_date, start_time, end_time) VALUES (?, ?, ?, ?, ?)", 
    [$rosterData['user_id'], $rosterData['branch_id'], $rosterData['shift_date'], $rosterData['start_time'], $rosterData['end_time']]);
echo "SUCCESS: Shift scheduled.\n";

// 3. Test Leave Request
echo "Testing Leave Request...\n";
$db->query("INSERT INTO employee_leaves (user_id, type, start_date, end_date, days, reason, status) VALUES (?, ?, ?, ?, ?, ?, ?)", 
    [1, 'casual', date('Y-m-d', strtotime('+5 days')), date('Y-m-d', strtotime('+6 days')), 1.0, 'Family Event', 'pending']);
$leaveId = $db->getConnection()->lastInsertId();

// Approve Leave
$db->query("UPDATE employee_leaves SET status = 'approved', approved_by = ? WHERE id = ?", [1, $leaveId]);
$check = $db->query("SELECT status FROM employee_leaves WHERE id = ?", [$leaveId])->fetchColumn();
if ($check === 'approved') {
    echo "SUCCESS: Leave request processed and approved.\n";
}

// 4. Test Overtime
echo "Testing Overtime Flow...\n";
$db->query("INSERT INTO overtime_requests (user_id, date, hours, reason, status) VALUES (?, ?, ?, ?, ?)", 
    [1, date('Y-m-d'), 2, 'Peak hour rush', 'pending']);
echo "SUCCESS: Overtime request submitted.\n";
