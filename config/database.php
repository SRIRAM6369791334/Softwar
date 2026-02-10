<?php

return [
    'host'     => \App\Core\Env::get('DB_HOST', '127.0.0.1'),
    'dbname'   => \App\Core\Env::get('DB_NAME', 'supermarket_db'),
    'username' => \App\Core\Env::get('DB_USER', 'root'),
    'password' => \App\Core\Env::get('DB_PASS', ''),
    'charset'  => 'utf8mb4',
];
