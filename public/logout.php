<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../app/auth.php';

if (!is_post()) {
    http_response_code(405);
    exit('Method Not Allowed');
}

csrf_check();
auth_logout();
$_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Вы вышли из системы.'];
redirect('/index.php');
