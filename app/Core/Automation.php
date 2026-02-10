<?php

namespace App\Core;

class Automation
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public static function trigger($event, $context = [])
    {
        $instance = new self();
        $instance->handleEvent($event, $context);
    }

    public function handleEvent($event, $context)
    {
        // 1. Find Active Workflows for this Event
        $workflows = $this->db->query("SELECT * FROM workflows WHERE trigger_event = ? AND is_active = 1", [$event])->fetchAll();

        foreach ($workflows as $wf) {
            $this->executeWorkflow($wf, $context);
        }
    }

    private function executeWorkflow($workflow, $context)
    {
        // 2. Get Actions
        $actions = $this->db->query("SELECT * FROM workflow_actions WHERE workflow_id = ? ORDER BY sort_order ASC", [$workflow['id']])->fetchAll();

        $logStatus = 'success';
        $logMessage = "Executed " . count($actions) . " actions.";

        foreach ($actions as $action) {
            try {
                $payload = json_decode($action['action_payload'], true);
                
                switch ($action['action_type']) {
                    case 'send_email':
                        $this->actionSendEmail($payload, $context);
                        break;
                    case 'create_notification':
                        $this->actionCreateNotification($payload, $context);
                        break;
                    case 'log_activity':
                        // Just log to file or debug
                        break;
                }
            } catch (\Exception $e) {
                $logStatus = 'failed';
                $logMessage = "Error in action {$action['action_type']}: " . $e->getMessage();
                break; // Stop workflow on error?
            }
        }

        // 3. Log Execution
        $this->db->query("INSERT INTO automation_logs (workflow_id, trigger_context, status, message) VALUES (?, ?, ?, ?)", 
            [$workflow['id'], json_encode($context), $logStatus, $logMessage]);
    }

    private function actionSendEmail($payload, $context)
    {
        // Resolve Recipient
        // If recipient_field is 'email', look in context['email']
        $to = $context[$payload['recipient_field'] ?? 'email'] ?? null;
        
        if (!$to) throw new \Exception("No recipient found in context");

        // Resolve Content
        // For now, hardcode based on template_key or use context
        $subject = "Automation: " . ($payload['template_key'] ?? 'Notification');
        $body = "Hello " . ($context['full_name'] ?? 'User') . ",\n\nThis is an automated message triggered by " . ($context['trigger'] ?? 'system') . ".";
        
        // If it's welcome email, use the settings we setup in Phase 22
        if (($payload['template_key'] ?? '') === 'email_welcome') {
             // We can reuse Mailer::sendWelcomeEmail logic or just manually fetch settings
             // For simplicity, let's call Mailer::send which we created in Phase 23
             // But we need to fetch subject/body from settings first
             $db = Database::getInstance();
             $subject = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'email_welcome_subject'")->fetch()['setting_value'] ?? 'Welcome';
             $settingsBody = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'email_welcome_body'")->fetch()['setting_value'] ?? 'Welcome';
             
             // Replace
             $body = str_replace('{name}', $context['full_name'] ?? '', $settingsBody);
             $body = str_replace('{email}', $context['email'] ?? '', $body);
             $body = str_replace('{username}', $context['username'] ?? '', $body);
        }

        Mailer::send($to, $subject, $body);
    }

    private function actionCreateNotification($payload, $context)
    {
        // Insert into notifications table
        // Payload: title, message, link, type
        $title = $payload['title'] ?? 'System Alert';
        $message = $payload['message'] ?? 'Event triggered';
        
        // Context Replacement
        $message = str_replace('{name}', $context['full_name'] ?? '', $message);

        $this->db->query("INSERT INTO notifications (title, message, type, is_read) VALUES (?, ?, 'info', 0)", 
            [$title, $message]);
    }
}
