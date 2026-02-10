<?php
require 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();

echo "--- PRODUCTS TABLE ---\n";
$cols = $db->query("DESCRIBE products")->fetchAll();
foreach ($cols as $col) {
    echo "{$col['Field']} - {$col['Type']}\n";
}

echo "\n--- PRODUCT_BATCHES TABLE ---\n";
$cols = $db->query("DESCRIBE product_batches")->fetchAll();
foreach ($cols as $col) {
    echo "{$col['Field']} - {$col['Type']}\n";
}

echo "\n--- INVOICES TABLE ---\n";
$cols = $db->query("DESCRIBE invoices")->fetchAll();
foreach ($cols as $col) {
    echo "{$col['Field']} - {$col['Type']}\n";
}
