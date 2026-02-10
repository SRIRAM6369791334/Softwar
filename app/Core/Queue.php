<?php

namespace App\Core;

/**
 * Background Queue Manager [#72, #75]
 * Handles deferred task execution and retries.
 */
class Queue
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Add a job to the queue
     */
    public function push(string $type, array $payload, string $runAt = null): int
    {
        $this->db->query(
            "INSERT INTO background_jobs (job_type, payload, run_at) VALUES (?, ?, ?)",
            [$type, json_encode($payload), $runAt ?? date('Y-m-d H:i:s')]
        );
        return (int)$this->db->lastInsertId();
    }

    /**
     * Process pending jobs (to be called by cron/scheduler)
     */
    public function process(int $limit = 5): int
    {
        $processed = 0;
        $jobs = $this->db->query(
            "SELECT * FROM background_jobs WHERE status = 'pending' AND run_at <= NOW() LIMIT ?",
            [$limit]
        )->fetchAll();

        foreach ($jobs as $job) {
            $this->executeJob($job);
            $processed++;
        }
        return $processed;
    }

    private function executeJob(array $job): void
    {
        $id = $job['id'];
        $this->db->query("UPDATE background_jobs SET status = 'processing', attempts = attempts + 1 WHERE id = ?", [$id]);

        try {
            $payload = json_decode($job['payload'], true);
            
            switch ($job['job_type']) {
                case 'email':
                    // Simulate email sending logic with retry support
                    $this->sendEmail($payload);
                    break;
                case 'data_export':
                    // Handle large file generations
                    break;
                case 'cleanup':
                    // Handle log rotations
                    break;
            }

            $this->db->query("UPDATE background_jobs SET status = 'completed' WHERE id = ?", [$id]);
        } catch (\Exception $e) {
            $status = ($job['attempts'] >= 3) ? 'failed' : 'pending';
            $this->db->query(
                "UPDATE background_jobs SET status = ?, last_error = ? WHERE id = ?",
                [$status, $e->getMessage(), $id]
            );
        }
    }

    private function sendEmail(array $payload): void
    {
        // Integration point for actual mailer
        if (empty($payload['to'])) throw new \Exception("Invalid email recipient.");
        // mail($payload['to'], $payload['subject'], $payload['body']);
    }
}
