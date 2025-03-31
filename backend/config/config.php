<?php

// Database configuration
define('DB_HOST', 'db');
define('DB_NAME', 'moddb');
define('DB_USER', 'vsmoddb');
define('DB_PASS', 'vsmoddb');

// Application configuration
define('APP_ENV', 'development'); // 'development' or 'production'
define('DEBUG', APP_ENV === 'development');
define('BASE_PATH', dirname(__DIR__));
define('ASSET_SERVER', '');

// CDN configuration
define('CDN', 'none'); // 'none' or 'bunny'

// For local development purposes create config/config.dev.php and put your config in there.
// That file is automatically ignored by version control.
if (file_exists(__DIR__ . '/config.dev.php')) {
    require_once __DIR__ . '/config.dev.php';
}

// Error reporting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}