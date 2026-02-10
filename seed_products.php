<?php
require 'app/bootstrap.php';

$db = \App\Core\Database::getInstance();
$pdo = $db->getConnection();

$csvDir = 'productlist/';
$tables = [
    'categories.csv' => 'categories',
    'subcategories.csv' => 'subcategories',
    'brands.csv' => 'brands',
    'subbrands.csv' => 'subbrands',
    'units.csv' => 'units',
    'storage_types.csv' => 'storage_types',
    'tax_slabs.csv' => 'tax_groups',
    'suppliers.csv' => 'suppliers',
    'products.csv' => 'products',
    'product_variants.csv' => 'product_variants'
];

echo "Starting seeding process...\n";

foreach ($tables as $file => $table) {
    $filePath = $csvDir . $file;
    if (!file_exists($filePath)) {
        echo "Warning: File $filePath not found. Skipping table $table.\n";
        continue;
    }

    echo "Seeding table: $table from $file...\n";
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, ",");
        
        // Prepare SQL
        $columns = implode("`, `", $headers);
        $placeholders = implode(", ", array_fill(0, count($headers), "?"));
        $sql = "INSERT INTO `$table` (`$columns`) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        
        $rowCount = 0;
        $pdo->beginTransaction();
        try {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Ensure data count matches headers
                if (count($data) != count($headers)) {
                    // Pad or truncate
                    $data = array_slice(array_pad($data, count($headers), null), 0, count($headers));
                }
                $stmt->execute($data);
                $rowCount++;
            }
            $pdo->commit();
            echo "Successfully seeded $rowCount rows into $table.\n";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Error seeding $table: " . $e->getMessage() . "\n";
        }
        fclose($handle);
    }
}

echo "Seeding completed.\n";
