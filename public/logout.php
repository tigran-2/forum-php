<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../app/auth.php';

auth_logout();
$_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Вы вышли из системы.'];
redirect('/index.php');
