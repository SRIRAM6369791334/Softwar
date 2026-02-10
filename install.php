<?php

echo "<h1>Installing Supermarket OS Database...</h1>";

try {
    // Load config
    $config = require __DIR__ . '/config/database.php';
    
    // Step 1: Connect WITHOUT database to create it
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Step 2: Create database if not exists
    $dbName = $config['dbname'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");
    echo "<p style='color:green'>✔ Database '$dbName' created/verified.</p>";
    
    // Step 3: Import schema
    $sql = file_get_contents(__DIR__ . '/database_schema.sql');
    $pdo->exec($sql);
    echo "<p style='color:green'>✔ Database Schema Imported Successfully.</p>";

    // Step 4: Create Default Admin User
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->query("SELECT id FROM users WHERE username = 'admin'");
    
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (role_id, username, password_hash, full_name) VALUES (1, 'admin', ?, 'Super Admin')");
        $stmt->execute([$password]);
        echo "<p style='color:green'>✔ Default User Created: <b>admin</b> / <b>admin123</b></p>";
    } else {
        echo "<p style='color:orange'>⚠ Admin user already exists.</p>";
    }
    
    echo "<br><hr><p style='color:green; font-size:1.2rem;'><b>✅ Installation Complete!</b></p>";
    echo "<p>You can now: <a href='/login' style='color:#00f3ff'>Login to the System →</a></p>";

} catch (Exception $e) {
    echo "<p style='color:red'>✖ Error: " . $e->getMessage() . "</p>";
}
