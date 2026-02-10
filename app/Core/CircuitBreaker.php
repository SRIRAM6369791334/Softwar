<?php

namespace App\Core;

/**
 * Circuit Breaker Pattern [#76]
 * Prevents cascading failures when external APIs are slow or down.
 */
class CircuitBreaker
{
    private $db;
    private $service;
    private $threshold = 5; // Fail after 5 errors
    private $timeout = 600; // Reset after 10 minutes

    public function __construct(string $service)
    {
        $this->db = Database::getInstance();
        $this->service = $service;
    }

    /**
     * Check if the circuit is open (blocked)
     */
    public function isOpen(): bool
    {
        $state = $this->db->query(
            "SELECT setting_value FROM system_settings WHERE setting_key = ?",
            ["circuit_breaker_{$this->service}"]
        )->fetch();

        if (!$state) return false;

        $data = json_decode($state['setting_value'], true);
        if ($data['status'] === 'open') {
            if (time() - $data['last_failure'] > $this->timeout) {
                $this->halfOpen();
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Record a success
     */
    public function success(): void
    {
        $this->db->query(
            "DELETE FROM system_settings WHERE setting_key = ?",
            ["circuit_breaker_{$this->service}"]
        );
    }

    /**
     * Record a failure
     */
    public function failure(): void
    {
        $state = $this->db->query(
            "SELECT setting_value FROM system_settings WHERE setting_key = ?",
            ["circuit_breaker_{$this->service}"]
        )->fetch();

        $data = $state ? json_decode($state['setting_value'], true) : ['failures' => 0, 'status' => 'closed'];
        $data['failures']++;
        $data['last_failure'] = time();

        if ($data['failures'] >= $this->threshold) {
            $data['status'] = 'open';
            (new ActivityMonitor())->logAdminAction(0, 'CIRCUIT_OPEN', 'SYSTEM', "Circuit breaker open for {$this->service}");
        }

        $this->db->query(
            "INSERT INTO system_settings (setting_key, setting_value, data_type) 
             VALUES (?, ?, 'json') ON DUPLICATE KEY UPDATE setting_value = ?",
            ["circuit_breaker_{$this->service}", json_encode($data), json_encode($data)]
        );
    }

    private function halfOpen(): void
    {
        $this->db->query(
            "UPDATE system_settings SET setting_value = JSON_SET(setting_value, '$.status', 'half-open') 
             WHERE setting_key = ?",
            ["circuit_breaker_{$this->service}"]
        );
    }
}
