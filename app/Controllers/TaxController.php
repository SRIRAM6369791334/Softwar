<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class TaxController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole(1); // Admin only
    }

    public function index()
    {
        $taxes = $this->db->query("SELECT * FROM tax_groups ORDER BY id ASC")->fetchAll();
        return $this->view('admin/taxes/index', ['taxes' => $taxes], 'dashboard');
    }

    public function create()
    {
        return $this->view('admin/taxes/create', [], 'dashboard');
    }

    public function store()
    {
        $name = $_POST['name'];
        $rate = $_POST['rate']; // e.g. 18.00

        $this->db->query("INSERT INTO tax_groups (name, rate) VALUES (?, ?)", [$name, $rate]);
        $this->redirect('/admin/taxes?success=Tax Group Added');
    }

    public function edit($id)
    {
        $tax = $this->db->query("SELECT * FROM tax_groups WHERE id = ?", [$id])->fetch();
        if (!$tax) $this->redirect('/admin/taxes');
        return $this->view('admin/taxes/edit', ['tax' => $tax], 'dashboard');
    }

    public function update($id)
    {
        $name = $_POST['name'];
        $rate = $_POST['rate'];

        $this->db->query("UPDATE tax_groups SET name = ?, rate = ? WHERE id = ?", [$name, $rate, $id]);
        $this->redirect('/admin/taxes?success=Tax Group Updated');
    }
}
