<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class NotificationController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Fetch unread notifications for the current user/branch
     */
    public function getUnread()
    {
        header('Content-Type: application/json');
        
        $branchId = Auth::getCurrentBranch();
        
        $notifications = $this->db->query("
            SELECT * FROM notifications 
            WHERE (branch_id = ? OR branch_id IS NULL)
            AND is_read = 0
            ORDER BY created_at DESC
            LIMIT 10
        ", [$branchId])->fetchAll();

        echo json_encode($notifications);
        exit;
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE id = ?", [$id]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    /**
     * Mark all as read for current branch
     */
    public function markAllRead()
    {
        $branchId = Auth::getCurrentBranch();
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE branch_id = ? OR branch_id IS NULL", [$branchId]);
        $this->redirect('/dashboard');
    }

    /**
     * Trigger notification generation (Low stock & Expiry)
     * This would ideally run via CRON, but we can trigger it on dashboard load
     */
    public static function checkAlerts()
    {
        $db = Database::getInstance();
        $branchId = Auth::getCurrentBranch();

        // 1. Low Stock Check
        $lowStock = $db->query("
            SELECT p.name, COALESCE(SUM(pb.stock_qty), 0) as current_qty, p.min_stock_alert
            FROM products p
            LEFT JOIN product_batches pb ON p.id = pb.product_id AND pb.branch_id = ?
            WHERE p.branch_id = ? AND p.is_active = 1
            GROUP BY p.id
            HAVING current_qty < p.min_stock_alert
        ", [$branchId, $branchId])->fetchAll();

        foreach ($lowStock as $item) {
            self::push([
                'type' => 'stock',
                'branch_id' => $branchId,
                'title' => 'Low Stock: ' . $item['name'],
                'message' => "Only {$item['current_qty']} units remaining (Alert threshold: {$item['min_stock_alert']})",
                'link' => '/inventory'
            ]);
        }

        // 2. Expiry Check (Next 30 days)
        $expiring = $db->query("
            SELECT p.name, pb.batch_no, pb.expiry_date
            FROM product_batches pb
            JOIN products p ON pb.product_id = p.id
            WHERE pb.branch_id = ? 
              AND pb.expiry_date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)
              AND pb.stock_qty > 0
        ", [$branchId])->fetchAll();

        foreach ($expiring as $batch) {
            self::push([
                'type' => 'expiry',
                'branch_id' => $branchId,
                'title' => 'Expiring Soon: ' . $batch['name'],
                'message' => "Batch {$batch['batch_no']} expires on {$batch['expiry_date']}",
                'link' => '/inventory'
            ]);
        }
    }

    /**
     * Static helper to push a notification
     */
    public static function push($data)
    {
        $db = Database::getInstance();
        
        // Prevent duplicate alerts for the same thing in the same day
        $exists = $db->query("
            SELECT id FROM notifications 
            WHERE title = ? AND DATE(created_at) = CURRENT_DATE AND is_read = 0
            LIMIT 1
        ", [$data['title']])->fetch();

        if (!$exists) {
            $db->query("
                INSERT INTO notifications (branch_id, type, title, message, link)
                VALUES (?, ?, ?, ?, ?)
            ", [
                $data['branch_id'] ?? null,
                $data['type'] ?? 'system',
                $data['title'],
                $data['message'],
                $data['link'] ?? null
            ]);
        }
    }

    /**
     * Daily Summary for Owner
     */
    public function dailySummary()
    {
        $branchId = Auth::getCurrentBranch();
        
        $summary = $this->db->query("
            SELECT type, COUNT(*) as count 
            FROM notifications 
            WHERE (branch_id = ? OR branch_id IS NULL)
              AND DATE(created_at) = CURRENT_DATE
            GROUP BY type
        ", [$branchId])->fetchAll();

        return $this->view('notifications/summary', ['summary' => $summary], 'dashboard');
    }
}
