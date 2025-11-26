<?php
require_once __DIR__ . '/../app/helpers.php';
$u = current_user();
$f = flash();
?><!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'Forum') ?></title>
  <style>
    :root { --bg:#0b1220; --card:#0f1a30; --text:#e8eefc; --muted:#9fb0d0; --line:#1d2a49; --acc:#6aa7ff; --err:#ff6a6a; }
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; background:linear-gradient(180deg, var(--bg), #070b14); color:var(--text); }
    a { color:var(--acc); text-decoration:none; }
    a:hover { text-decoration:underline; }
    .wrap { max-width: 980px; margin: 0 auto; padding: 24px; }
    .nav { display:flex; align-items:center; justify-content:space-between; gap:12px; padding: 12px 16px; border:1px solid var(--line); border-radius:16px; background:rgba(15,26,48,.75); backdrop-filter: blur(8px); }
    .nav .left { display:flex; align-items:center; gap:14px; font-weight:700; }
    .nav .right { display:flex; align-items:center; gap:10px; color:var(--muted); }
    .pill { padding:6px 10px; border:1px solid var(--line); border-radius:999px; background: rgba(255,255,255,.03); }
    .grid { margin-top: 16px; display:grid; grid-template-columns: 1fr; gap: 14px; }
    .card { border:1px solid var(--line); border-radius:18px; background: rgba(15,26,48,.75); padding: 16px; }
    .h1 { font-size: 22px; margin: 0 0 10px; }
    .muted { color: var(--muted); font-size: 14px; }
    .btn { display:inline-block; padding:10px 14px; border-radius:12px; border:1px solid var(--line); background: rgba(255,255,255,.04); color:var(--text); cursor:pointer; }
    .btn:hover { background: rgba(255,255,255,.07); }
    .btn.primary { border-color: rgba(106,167,255,.35); background: rgba(106,167,255,.12); }
    .btn.primary:hover { background: rgba(106,167,255,.18); }
    input, textarea { width:100%; box-sizing:border-box; padding:10px 12px; border-radius:12px; border:1px solid var(--line); background: rgba(0,0,0,.15); color: var(--text); }
    textarea { min-height: 140px; resize: vertical; }
    label { display:block; margin: 10px 0 6px; color: var(--muted); font-size:14px; }
    .row { display:grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    @media (max-width:720px){ .row{ grid-template-columns:1fr; } }
    .err { color: var(--err); font-size: 13px; margin-top: 6px; }
    .flash { padding:10px 12px; border-radius:14px; border:1px solid var(--line); margin-top: 14px; }
    .flash.error { border-color: rgba(255,106,106,.4); background: rgba(255,106,106,.08); }
    .flash.ok { border-color: rgba(106,255,173,.3); background: rgba(106,255,173,.08); }
    .topic-title { font-size: 18px; margin: 0; }
    .topic-meta { margin-top: 4px; }
    .hr { height:1px; background: var(--line); margin: 12px 0; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="nav">
      <div class="left">
        <a href="/index.php">Forum</a>
        <span class="pill"><a href="/index.php">Темы</a></span>
        <?php if ($u): ?>
          <span class="pill"><a href="/create_topic.php">Создать тему</a></span>
        <?php endif; ?>
      </div>
      <div class="right">
        <?php if ($u): ?>
          <span class="pill">Вы: <?= e($u['first_name'] . ' ' . $u['last_name']) ?></span>
          <form action="/logout.php" method="post" style="margin:0;">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <button class="btn" type="submit">Выйти</button>
          </form>
        <?php else: ?>
          <a class="btn" href="/login.php">Войти</a>
          <a class="btn primary" href="/register.php">Регистрация</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($f): ?>
      <div class="flash <?= e($f['type'] ?? 'ok') ?>">
        <?= e($f['msg'] ?? '') ?>
      </div>
    <?php endif; ?>
