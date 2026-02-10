<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class SettingsController extends Controller
{
    private $db;

    public function __construct()
    {
        // Ensure only Admins can access settings
        if (!Auth::check() || Auth::user()['role_id'] != 1) {
            header('Location: /login');
            exit;
        }

        $this->db = Database::getInstance();
    }

    public function index()
    {
        // Fetch all settings ordered by group
        $stmt = $this->db->query("
            SELECT * FROM settings 
            ORDER BY FIELD(setting_group, 'general', 'store', 'branding', 'security', 'automation', 'email', 'invoice'), id ASC
        ");
        $allSettings = $stmt->fetchAll();

        // Group them for the view
        $groupedSettings = [];
        foreach ($allSettings as $setting) {
            $groupedSettings[$setting['setting_group']][] = $setting;
        }

        return $this->view('admin/settings/index', [
            'groupedSettings' => $groupedSettings,
            'title' => 'System Settings'
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = $this->db->getConnection();
            
            try {
                $pdo->beginTransaction();

                foreach ($_POST as $key => $value) {
                    // Update each setting based on key
                    // Note: Checkboxes not checked don't send POST data, handled below
                    $stmt = $this->db->query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
                }

                // Handle boolean toggles (checkboxes) that might be missing from POST
                // We fetch all boolean settings and check if they exist in POST
                $boolSettings = $this->db->query("SELECT setting_key FROM settings WHERE input_type = 'boolean'")->fetchAll();
                foreach ($boolSettings as $s) {
                    $key = $s['setting_key'];
                    if (!isset($_POST[$key])) {
                        // If missing, it means it was unchecked -> set to 0
                        $this->db->query("UPDATE settings SET setting_value = '0' WHERE setting_key = ?", [$key]);
                    }
                }

                $pdo->commit();
                
                // Redirect back with success message
                header('Location: /admin/settings?success=1');
                exit;

            } catch (\Exception $e) {
                $pdo->rollBack();
                die("Error updating settings: " . $e->getMessage());
            }
        }
    }
}
