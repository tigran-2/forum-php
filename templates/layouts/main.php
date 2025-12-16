<?php
declare(strict_types=1);

use App\Core\Session;
use function App\Core\e;
use function App\Core\csrf_field;
use function App\Core\current_user;
use function App\Core\flash;
use function App\Core\url;

$user = current_user();
$flash = flash();
?><!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Форум для обсуждения различных тем">
  <title><?= e($title ?? 'Forum') ?></title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <div class="wrap">
    <nav class="nav">
      <div class="left">
        <a href="/">Forum</a>
        <span class="pill"><a href="/">Темы</a></span>
        <?php if ($user): ?>
          <span class="pill"><a href="/topics/create">Создать тему</a></span>
        <?php endif; ?>
      </div>
      <div class="right">
        <?php if ($user): ?>
          <span class="pill"><a href="/profile/<?= (int)$user['id'] ?>">Вы: <?= e($user['first_name'] . ' ' . $user['last_name']) ?></a></span>
          <form action="/logout" method="post" style="margin:0;">
            <?= csrf_field() ?>
            <button class="btn" type="submit">Выйти</button>
          </form>
        <?php else: ?>
          <a class="btn" href="/login">Войти</a>
          <a class="btn primary" href="/register">Регистрация</a>
        <?php endif; ?>
      </div>
    </nav>

    <?php if ($flash): ?>
      <div class="flash <?= e($flash['type'] ?? 'ok') ?>">
        <?= e($flash['msg'] ?? '') ?>
      </div>
    <?php endif; ?>

    <main>
      <?= $content ?? '' ?>
    </main>

    <footer class="footer">
      Учебный проект: темы и комментарии. PHP + MySQL.
    </footer>
  </div>
</body>
</html>
