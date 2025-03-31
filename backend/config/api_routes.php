<?php

use App\Core\App;
use App\Controllers\Api\V1\TagController;
use App\Controllers\Api\V1\ModController;
use App\Controllers\Api\V1\AuthorController;
use App\Controllers\Api\V1\CommentController;
use App\Controllers\Api\V1\ChangelogController;
use App\Controllers\Api\V1\GameVersionController;
use App\Controllers\Api\V2\NotificationController;

$router = App::get('router');

// V1 API Routes
$router->group('/api', function($router) {
    // Tags endpoint
    $router->get('/tags', [TagController::class, 'index']);
    
    // Game versions endpoint
    $router->get('/gameversions', [GameVersionController::class, 'index']);
    
    // Authors endpoint
    $router->get('/authors', [AuthorController::class, 'index']);
    
    // Comments endpoints
    $router->get('/comments', [CommentController::class, 'index']);
    $router->get('/comments/{assetid}', [CommentController::class, 'forAsset']);
    
    // Changelogs endpoints
    $router->get('/changelogs', [ChangelogController::class, 'index']);
    $router->get('/changelogs/{assetid}', [ChangelogController::class, 'forAsset']);
    
    // Mods endpoints
    $router->get('/mods', [ModController::class, 'index']);
    $router->get('/mod/{modid}', [ModController::class, 'show']);
});

// V2 API Routes
$router->group('/api/v2', function($router) {
    // Notifications endpoints (auth required)
    $router->get('/notifications', [NotificationController::class, 'index'])->middleware('auth');
    $router->get('/notifications/{id}', [NotificationController::class, 'show'])->middleware('auth');
    $router->get('/notifications/all', [NotificationController::class, 'all'])->middleware('auth');
    $router->post('/notifications/clear', [NotificationController::class, 'clear'])->middleware('auth');

    // Changelog endpoints
    $router->get('/changelogs', [ChangelogController::class, 'index']);
});