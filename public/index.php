<?php
declare(strict_types=1);

/**
 * Single entry point for the application.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\App;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\TopicController;
use App\Controllers\CommentController;
use App\Controllers\ProfileController;

// Create and boot application
$app = App::getInstance();
$app->setDebug((bool)getenv('APP_DEBUG'));
$app->boot();

// Get router
$router = $app->router();

// Define routes

// Home
$router->get('/', [new HomeController(), 'index']);

// Auth
$router->get('/login', [new AuthController(), 'showLogin']);
$router->post('/login', [new AuthController(), 'login']);
$router->get('/register', [new AuthController(), 'showRegister']);
$router->post('/register', [new AuthController(), 'register']);
$router->post('/logout', [new AuthController(), 'logout']);

// Topics
$router->get('/topics/create', [new TopicController(), 'create'], ['auth']);
$router->post('/topics', [new TopicController(), 'store'], ['auth']);
$router->get('/topics/{id}', [new TopicController(), 'show']);
$router->post('/topics/{id}/comments', [new TopicController(), 'addComment'], ['auth']);
$router->get('/topics/{id}/edit', [new TopicController(), 'edit'], ['auth']);
$router->post('/topics/{id}/update', [new TopicController(), 'update'], ['auth']);
$router->post('/topics/{id}/delete', [new TopicController(), 'delete'], ['auth']);

// Comments
$router->get('/comments/{id}/edit', [new CommentController(), 'edit'], ['auth']);
$router->post('/comments/{id}/update', [new CommentController(), 'update'], ['auth']);
$router->post('/comments/{id}/delete', [new CommentController(), 'delete'], ['auth']);

// Profile
$router->get('/profile/edit', [new ProfileController(), 'edit'], ['auth']);
$router->post('/profile/update', [new ProfileController(), 'update'], ['auth']);
$router->get('/profile/{id}', [new ProfileController(), 'show']);

// Backward compatibility for old URLs
$router->get('/index.php', function() {
    header('Location: /');
    exit;
});
$router->get('/login.php', function() {
    header('Location: /login');
    exit;
});
$router->get('/register.php', function() {
    header('Location: /register');
    exit;
});
$router->get('/topic.php', function() {
    $id = (int)($_GET['id'] ?? 0);
    header("Location: /topics/{$id}");
    exit;
});
$router->get('/create_topic.php', function() {
    header('Location: /topics/create');
    exit;
});

// Run application
$app->run();
