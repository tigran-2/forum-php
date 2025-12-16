<?php
declare(strict_types=1);

use function App\Core\e;
use function App\Core\csrf_field;
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Вход</h1>
    <form method="post" action="/login" novalidate>
      <?= csrf_field() ?>

      <label>Email</label>
      <input name="email" type="email" value="<?= e($old_email ?? '') ?>" required autocomplete="email">

      <label>Пароль</label>
      <input name="password" type="password" required autocomplete="current-password">

      <?php if (!empty($error)): ?>
        <div class="err mt-2"><?= e($error) ?></div>
      <?php endif; ?>

      <div class="actions">
        <button class="btn primary" type="submit">Войти</button>
        <span class="muted">Нет аккаунта? <a href="/register">Регистрация</a></span>
      </div>
    </form>
  </div>
</div>
