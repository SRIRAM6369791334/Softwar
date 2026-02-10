<?php
// 1. Define Root Path
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// 2. Autoloader (Simple PSR-4 Implementation from index.php)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_ROOT . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 3. Composer Autoloader (Optional)
if (file_exists(APP_ROOT . '/vendor/autoload.php')) {
    require_once APP_ROOT . '/vendor/autoload.php';
}

// 4. Load Environment
\App\Core\Env::load(APP_ROOT . '/.env');

// 5. App Constants
if (!defined('APP_ENV')) {
    define('APP_ENV', \App\Core\Env::get('APP_ENV', 'production'));
}
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', \App\Core\Env::get('APP_DEBUG', false));
}

// 5. Initialize Core Components
// $db = \App\Core\Database::getInstance();

// 6. Helper Functions
if (file_exists(__DIR__ . '/Core/Helpers.php')) {
    require_once __DIR__ . '/Core/Helpers.php';
}

// 7. Register Global Error Handler
\App\Core\ErrorHandler::getInstance()->register();
