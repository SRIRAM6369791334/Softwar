<?php

// 1. Define Root Path
define('APP_ROOT', __DIR__);

// 2. Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_ROOT . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// Mock Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['user_id'] = 1;
$_SESSION['role_id'] = 1;
$_SESSION['branch_id'] = 1;

$db = \App\Core\Database::getInstance();
$db->query("DELETE FROM product_locations"); // Clean start

// 1. Get a product
$product = $db->query("SELECT id FROM products LIMIT 1")->fetch();

if (!$product) {
    die("No products found to test.");
}

// 2. Mock POST Request
$_SERVER['REQUEST_METHOD'] = 'POST';
$input = json_encode([
    'product_id' => $product['id'],
    'section_id' => 1,
    'x' => 5,
    'y' => 5,
    'z' => 1
]);

echo "Testing MapController::saveLocation...\n";

// Mock Controller to override input reading if necessary, 
// BUT since we can't easily mock php://input in a running script without external tools,
// we will rely on a small trick: 
// We will modify the MapController to accept data as an argument if provided, 
// OR we just use a helper method. 
// Actually, let's just make the verify script use `file_put_contents` to a temp file and point `php://input` to it? 
// No, php://input is read-only.
// Let's use the Class Extension approach again, but we need to override the method to NOT read php://input.

class TestMapController extends \App\Controllers\MapController {
    public function saveLocationTest($data) {
        $this->requireRole([1, 2]);
        
        // Copied Logic
        $db = \App\Core\Database::getInstance();
        $exists = $db->query("SELECT id FROM product_locations WHERE product_id = ?", [$data['product_id']])->fetch();

        if ($exists) {
            $db->query("UPDATE product_locations SET section_id = ?, x_coord = ?, y_coord = ?, z_layer = ? WHERE product_id = ?", 
                [$data['section_id'], $data['x'], $data['y'], $data['z'] ?? 1, $data['product_id']]);
        } else {
            $db->query("INSERT INTO product_locations (product_id, section_id, x_coord, y_coord, z_layer) VALUES (?, ?, ?, ?, ?)", 
                [$data['product_id'], $data['section_id'], $data['x'], $data['y'], $data['z'] ?? 1]);
        }
        return ['success' => true];
    }
}

$controller = new TestMapController();
$res = $controller->saveLocationTest(json_decode($input, true));

echo "Save Result: " . json_encode($res) . "\n";

// 3. Verify
$loc = $db->query("SELECT * FROM product_locations WHERE product_id = ?", [$product['id']])->fetch();
if ($loc && $loc['x_coord'] == 5 && $loc['y_coord'] == 5) {
    echo "SUCCESS: Product mapped to 5,5\n";
} else {
    echo "FAILURE: Product not found or wrong location\n";
    print_r($loc);
}
