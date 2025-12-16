<?php
// config/config.php
declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'forum_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

return [
  'db' => [
    'dsn' => $dsn,
    'user' => $user,
    'pass' => $pass,
    'options' => [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]
  ],
  
  'app' => [
    'name' => 'Forum',
    'debug' => (bool)getenv('APP_DEBUG'),
    'url' => getenv('APP_URL') ?: 'http://localhost',
  ],
  
  'pagination' => [
    'topics_per_page' => 10,
    'comments_per_page' => 20,
  ],
  
  'security' => [
    'rate_limit_attempts' => 5,
    'rate_limit_window' => 300, // seconds
  ],
];
