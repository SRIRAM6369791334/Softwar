<?php

namespace App\Core;

class Mailer
{
    public static function send($to, $subject, $body)
    {
        // 1. Fetch SMTP Settings
        $db = Database::getInstance();
        $settings = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'email'")->fetchAll();
        $config = [];
        foreach($settings as $s) $config[$s['setting_key']] = $s['setting_value'];

        // 2. Simulate Sending (Log to file)
        // In a real app, use PHPMailer or SwiftMailer here with $config details
        
        $logEntry = "--- EMAIL SENT [" . date('Y-m-d H:i:s') . "] ---\n";
        $logEntry .= "To: $to\n";
        $logEntry .= "Subject: $subject\n";
        $logEntry .= "Body:\n$body\n";
        $logEntry .= "SMTP Config: " . json_encode($config) . "\n";
        $logEntry .= "-----------------------------------------\n\n";

        file_put_contents(__DIR__ . '/../../logs/email.log', $logEntry, FILE_APPEND);

        return true;
    }

    public static function sendWelcomeEmail($user)
    {
        $db = Database::getInstance();
        
        // Fetch Template
        $subject = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'email_welcome_subject'")->fetch()['setting_value'] ?? 'Welcome';
        $body = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'email_welcome_body'")->fetch()['setting_value'] ?? 'Welcome {name}';

        // Replace Placeholders
        $body = str_replace('{name}', $user['full_name'], $body);
        $body = str_replace('{email}', $user['email'], $body);
        $body = str_replace('{username}', $user['username'], $body);
        $body = str_replace('{password}', $user['password_plain'] ?? '********', $body); // Only if we assume it's passed

        return self::send($user['email'], $subject, $body);
    }
}
