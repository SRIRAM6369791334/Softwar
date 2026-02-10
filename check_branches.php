<?php
require 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();
echo "--- BRANCHES TABLE ---\n";
$cols = $db->query("DESCRIBE branches")->fetchAll();
foreach ($cols as $col) {
    echo "{$col['Field']} - {$col['Type']}\n";
}
