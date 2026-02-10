<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Automation;

/**
 * Activity Monitor
 * Detects suspicious behavior and triggers security alerts/actions
 */
class ActivityMonitor
{
    private $db;
    
    // Configurable thresholds
    private const LARGE_TRANSACTION_THRESHOLD = 5000; // e.g., $5000
    private const ODD_HOURS_START = 22; // 10 PM
    private const ODD_HOURS_END = 6;    // 6 AM
    private const RAPID_REFUND_THRESHOLD = 3; // 3 refunds in 10 minutes
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Monitor a POS Transaction for anomalies
     */
    public function monitorTransaction(array $data): void
    {
        $riskScore = 0;
        $reasons = [];
        
        // 1. Check for Large Transaction
        if (($data['grand_total'] ?? 0) > self::LARGE_TRANSACTION_THRESHOLD) {
            $riskScore += 40;
            $reasons[] = "Large transaction amount: " . ($data['grand_total'] ?? 0);
        }
        
        // 2. Check for Odd Hours
        $hour = (int)date('H');
        if ($hour >= self::ODD_HOURS_START || $hour < self::ODD_HOURS_END) {
            $riskScore += 30;
            $reasons[] = "Transaction at unusual hour: " . date('H:i');
        }
        
        // 3. Check for Rapid Successions (Refunds/Voids)
        if (isset($data['type']) && $data['type'] === 'refund') {
            $recentRefunds = $this->db->query(
                "SELECT COUNT(*) as count FROM refund_requests WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)",
                [$data['user_id']]
            )->fetch();
            
            if ($recentRefunds['count'] >= self::RAPID_REFUND_THRESHOLD) {
                $riskScore += 50;
                $reasons[] = "Rapid refund attempts: " . $recentRefunds['count'] . " in 10 mins";
            }
        }
        
        // Trigger Alert if Risk is High
        if ($riskScore >= 40) {
            $this->triggerRiskAlert('high_value_transaction', [
                'risk_score' => $riskScore,
                'reasons' => $reasons,
                'data' => $data
            ]);
        }
    }
    
    /**
     * Monitor Login Activity
     */
    public function monitorLogin(array $data): void
    {
        // Check for multiple failed attempts from same IP (audit log check)
        $failedAttempts = $this->db->query(
            "SELECT COUNT(*) as count FROM login_history WHERE ip_address = ? AND status = 'failed' AND login_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
            [$data['ip_address']]
        )->fetch();
        
        if ($failedAttempts['count'] >= 5) {
            $this->triggerRiskAlert('brute_force_detected', [
                'ip' => $data['ip_address'],
                'attempts' => $failedAttempts['count'],
                'user_id' => $data['user_id'] ?? null
            ]);
        }
    }
    
    /**
     * Trigger a risk alert via the Automation system
     */
    private function triggerRiskAlert(string $eventType, array $context): void
    {
        // Log to admin_actions/audit_logs first
        $this->db->query(
            "INSERT INTO admin_actions (user_id, action, target_type, details, ip_address) VALUES (?, ?, ?, ?, ?)",
            [
                $context['user_id'] ?? 0,
                'RISK_ALERT',
                $eventType,
                json_encode($context),
                $_SERVER['REMOTE_ADDR'] ?? 'system'
            ]
        );
        
        // Trigger Automation engine
        Automation::trigger('security_risk_detected', array_merge(['event_type' => $eventType], $context));
    }
    
    /**
     * Log Administrative Actions (used by multiple components)
     * This is the missing method that was causing crashes
     */
    public function logAdminAction(int $userId, string $action, string $targetType, string $details): void
    {
        $this->db->query(
            "INSERT INTO admin_actions (user_id, action, target_type, details, ip_address) VALUES (?, ?, ?, ?, ?)",
            [
                $userId,
                $action,
                $targetType,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'CLI'
            ]
        );
    }
    
    /**
     * Generate a summary of anomalies for the last 24 hours
     */
    public function generateDailyAnomalySummary(): void
    {
        $yesterday = date('Y-m-d H:i:s', strtotime('-24 hours'));
        
        $anomalies = $this->db->query(
            "SELECT action, target_type, details, created_at, ip_address 
             FROM admin_actions 
             WHERE action = 'RISK_ALERT' AND created_at >= ?",
            [$yesterday]
        )->fetchAll();
        
        if (empty($anomalies)) {
            return; // Nothing to report
        }
        
        $summary = [
            'total_alerts' => count($anomalies),
            'types' => [],
            'top_ips' => []
        ];
        
        foreach ($anomalies as $alert) {
            $type = $alert['target_type'];
            $summary['types'][$type] = ($summary['types'][$type] ?? 0) + 1;
            
            $ip = $alert['ip_address'];
            $summary['top_ips'][$ip] = ($summary['top_ips'][$ip] ?? 0) + 1;
        }
        
        // Trigger generic automation for Daily Report
        Automation::trigger('daily_security_summary', [
            'date' => date('Y-m-d'),
            'summary' => $summary,
            'details' => $anomalies
        ]);
    }

    /**
     * System Health Monitor (Disk, DB, etc.)
     */
    public function checkSystemHealth(): array
    {
        $status = 'healthy';
        $issues = [];
        
        // Check Disk Space
        $free = disk_free_space(APP_ROOT);
        $total = disk_total_space(APP_ROOT);
        $percentFree = ($free / $total) * 100;
        
        if ($percentFree < 10) {
            $status = 'critical';
            $issues[] = "Low disk space: " . round($percentFree, 2) . "% remaining";
            Automation::trigger('system_health_alert', ['type' => 'low_disk', 'value' => $percentFree]);
        }
        
        // Check DB Connectivity
        try {
            $this->db->query("SELECT 1")->fetch();
        } catch (\Exception $e) {
            $status = 'critical';
            $issues[] = "Database connectivity issue: " . $e->getMessage();
            // Note: If DB is down, we can't trigger internal DB-driven automation,
            // but we could try sending a direct email/SMS here if fallback exists.
        }
        
        return [
            'status' => $status,
            'issues' => $issues,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
