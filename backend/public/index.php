<?php
// Bootstrap the application
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use App\Core\App;

// Remove server signature
header_remove('X-Powered-By');

// Initialize the application
$app = new App();

// Load routes
require_once __DIR__ . '/../config/routes.php';

// Run the application
$app->run();