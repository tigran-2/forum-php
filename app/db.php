<?php
// app/db.php
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;

    $config = require __DIR__ . '/../config/config.php';
    $db = $config['db'];

    $pdo = new PDO($db['dsn'], $db['user'], $db['pass'], $db['options']);
    return $pdo;
}
