<?php
// public/bootstrap.php
declare(strict_types=1);

use Dotenv\Dotenv;

// Harden session cookie defaults.
$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'secure' => $secure,
    'samesite' => 'Lax',
]);

session_start();

// Load environment from .env if available.
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
    if (class_exists(Dotenv::class)) {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->safeLoad();
    }
}

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/db.php';

// Initialize CSRF token on first visit.
csrf_token();
