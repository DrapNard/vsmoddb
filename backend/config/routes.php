<?php

use App\Core\App;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ModController;
use App\Controllers\ReleaseController;
use App\Controllers\UserController;
use App\Controllers\CommentController;
use App\Controllers\TagController;
use App\Controllers\NotificationController;

$router = App::get('router');

// Home routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/home', [HomeController::class, 'index']);

// Authentication routes
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// User routes
$router->get('/user/{id}', [UserController::class, 'show']);
$router->get('/user/{id}/edit', [UserController::class, 'edit']);
$router->post('/user/{id}/update', [UserController::class, 'update']);
$router->get('/accountsettings', [UserController::class, 'settings']);
$router->post('/accountsettings', [UserController::class, 'updateSettings']);

// Mod routes
$router->get('/mod/{id}', [ModController::class, 'show']);
$router->get('/mod/{id}/edit', [ModController::class, 'edit']);
$router->post('/mod/{id}/update', [ModController::class, 'update']);
$router->get('/mod/create', [ModController::class, 'create']);
$router->post('/mod/store', [ModController::class, 'store']);

// Release routes
$router->get('/release/{id}', [ReleaseController::class, 'show']);
$router->get('/release/{id}/edit', [ReleaseController::class, 'edit']);
$router->post('/release/{id}/update', [ReleaseController::class, 'update']);
$router->get('/release/create', [ReleaseController::class, 'create']);
$router->post('/release/store', [ReleaseController::class, 'store']);

// Comment routes
$router->post('/comment/store', [CommentController::class, 'store']);
$router->get('/comment/{id}/edit', [CommentController::class, 'edit']);
$router->post('/comment/{id}/update', [CommentController::class, 'update']);
$router->post('/comment/{id}/delete', [CommentController::class, 'delete']);

// Tag routes
$router->get('/tag/{id}', [TagController::class, 'show']);
$router->get('/tags', [TagController::class, 'index']);

// Notification routes
$router->get('/notifications', [NotificationController::class, 'index']);
$router->post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);

// API routes
$router->get('/api/mods', [ModController::class, 'apiIndex']);
$router->get('/api/mod/{id}', [ModController::class, 'apiShow']);
$router->get('/api/users', [UserController::class, 'apiIndex']);
$router->get('/api/user/{id}', [UserController::class, 'apiShow']);