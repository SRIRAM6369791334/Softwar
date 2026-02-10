<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class MapController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->requireRole([1, 2, 3]); // Accessible to all roles (Cashiers need to see it)
    }

    public function index()
    {
        $branchId = Auth::getCurrentBranch();
        
        // Get Sections
        $sections = $this->db->query("SELECT * FROM map_sections WHERE branch_id = ?", [$branchId])->fetchAll();
        
        // If no sections exist, create a default one
        if (empty($sections)) {
            $this->db->query("INSERT INTO map_sections (branch_id, name, grid_width, grid_height) VALUES (?, 'Main Floor', 12, 12)", [$branchId]);
            $sections = $this->db->query("SELECT * FROM map_sections WHERE branch_id = ?", [$branchId])->fetchAll();
        }

        return $this->view('map/index', ['sections' => $sections], 'dashboard');
    }

    public function getMapData()
    {
        $branchId = Auth::getCurrentBranch();
        
        // Get Sections
        $sections = $this->db->query("SELECT * FROM map_sections WHERE branch_id = ?", [$branchId])->fetchAll();
        
        // Get Product Locations
        $locations = $this->db->query("
            SELECT pl.*, p.name, p.sku, p.unit, 
                   COALESCE(SUM(pb.stock_qty), 0) as stock
            FROM product_locations pl
            JOIN products p ON pl.product_id = p.id
            LEFT JOIN product_batches pb ON p.id = pb.product_id AND pb.branch_id = ?
            WHERE p.branch_id = ?
            GROUP BY pl.id
        ", [$branchId, $branchId])->fetchAll();

        header('Content-Type: application/json');
        echo json_encode([
            'sections' => $sections,
            'locations' => $locations
        ]);
        exit;
    }

    public function saveLocation()
    {
        // Only Admin/Manager can move things
        $this->requireRole([1, 2]);

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['product_id'], $data['section_id'], $data['x'], $data['y'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
            exit;
        }

        // Check if exists
        $exists = $this->db->query("SELECT id FROM product_locations WHERE product_id = ?", [$data['product_id']])->fetch();

        if ($exists) {
            $this->db->query("UPDATE product_locations SET section_id = ?, x_coord = ?, y_coord = ?, z_layer = ? WHERE product_id = ?", 
                [$data['section_id'], $data['x'], $data['y'], $data['z'] ?? 1, $data['product_id']]);
        } else {
            $this->db->query("INSERT INTO product_locations (product_id, section_id, x_coord, y_coord, z_layer) VALUES (?, ?, ?, ?, ?)", 
                [$data['product_id'], $data['section_id'], $data['x'], $data['y'], $data['z'] ?? 1]);
        }

        echo json_encode(['success' => true]);
        exit;
    }
    
    public function searchUnmapped()
    {
        $q = $_GET['q'] ?? '';
        $branchId = Auth::getCurrentBranch();
        
        // Use Unified Search Service
        // Note: The SearchService currently searches ALL products. 
        // In a real scenario, we'd pass branch_id filters to the search engine.
        // For now, we will filter the results in PHP or update the Engine.
        // Let's keep it simple for the "Integration" demo.
        
        $engine = \App\Services\Search\SearchService::getEngine();
        $results = $engine->search($q, 'products');

        // Filter out already mapped items for the CURRENT branch
        $mappedIds = $this->db->query("
            SELECT pl.product_id 
            FROM product_locations pl
            JOIN products p ON pl.product_id = p.id
            WHERE p.branch_id = ?
        ", [$branchId])->fetchAll(\PDO::FETCH_COLUMN);
        
        $finalResults = [];
        foreach ($results as $item) {
            // Also ensure the product itself belongs to this branch
            // Search engine might return all, so we must filter here for MVP
            if ($item['branch_id'] != $branchId) continue;
            
            if (!in_array($item['id'], $mappedIds)) {
                $finalResults[] = $item;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(array_slice($finalResults, 0, 10)); // Limit 10
        exit;
    }
}
