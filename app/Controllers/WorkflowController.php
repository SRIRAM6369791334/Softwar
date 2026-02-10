<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class WorkflowController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole(1); // Admin only
    }

    public function index()
    {
        $workflows = $this->db->query("SELECT * FROM workflows ORDER BY created_at DESC")->fetchAll();
        return $this->view('admin/workflows/index', ['workflows' => $workflows], 'dashboard');
    }

    public function create()
    {
        return $this->view('admin/workflows/create', [], 'dashboard');
    }

    public function store()
    {
        $name = $_POST['name'];
        $trigger = $_POST['trigger_event'];
        $desc = $_POST['description'];

        $this->db->query("INSERT INTO workflows (name, trigger_event, description) VALUES (?, ?, ?)", 
            [$name, $trigger, $desc]);
        
        $id = $this->db->getConnection()->lastInsertId();
        $this->redirect("/admin/workflows/edit/$id");
    }

    public function edit($id)
    {
        $workflow = $this->db->query("SELECT * FROM workflows WHERE id = ?", [$id])->fetch();
        if (!$workflow) $this->redirect('/admin/workflows');

        $actions = $this->db->query("SELECT * FROM workflow_actions WHERE workflow_id = ? ORDER BY sort_order ASC", [$id])->fetchAll();

        return $this->view('admin/workflows/edit', [
            'workflow' => $workflow,
            'actions' => $actions
        ], 'dashboard');
    }

    public function addAction($id)
    {
        $type = $_POST['action_type'];
        // Construct payload based on type
        $payload = [];
        
        if ($type === 'send_email') {
            $payload['recipient_field'] = $_POST['recipient_field'];
            $payload['template_key'] = $_POST['template_key'];
        } elseif ($type === 'create_notification') {
            $payload['title'] = $_POST['title'];
            $payload['message'] = $_POST['message'];
        }

        $json = json_encode($payload);
        $this->db->query("INSERT INTO workflow_actions (workflow_id, action_type, action_payload) VALUES (?, ?, ?)", 
            [$id, $type, $json]);

        $this->redirect("/admin/workflows/edit/$id?success=Action Added");
    }

    public function deleteAction($id)
    {
        // $id is action id, need to find workflow id to redirect back
        $action = $this->db->query("SELECT workflow_id FROM workflow_actions WHERE id = ?", [$id])->fetch();
        if ($action) {
            $this->db->query("DELETE FROM workflow_actions WHERE id = ?", [$id]);
            $this->redirect("/admin/workflows/edit/" . $action['workflow_id']);
        } else {
            $this->redirect("/admin/workflows");
        }
    }
}
