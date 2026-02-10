<?php
define('APP_ROOT', 'e:/New folder (3)');
require APP_ROOT . '/app/Core/Database.php';
require APP_ROOT . '/app/Core/Env.php';

try {
    $db = App\Core\Database::getInstance();
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
