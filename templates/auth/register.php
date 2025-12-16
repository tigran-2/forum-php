<?php
declare(strict_types=1);

use function App\Core\e;
use function App\Core\csrf_field;
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Регистрация</h1>
    <form method="post" action="/register" novalidate>
      <?= csrf_field() ?>

      <label>Email</label>
      <input name="email" type="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="email">
      <?php if (!empty($errors['email'])): ?><div class="err"><?= e($errors['email']) ?></div><?php endif; ?>

      <div class="row">
        <div>
          <label>Имя</label>
          <input name="first_name" value="<?= e($old['first_name'] ?? '') ?>" required autocomplete="given-name">
          <?php if (!empty($errors['first_name'])): ?><div class="err"><?= e($errors['first_name']) ?></div><?php endif; ?>
        </div>
        <div>
          <label>Фамилия</label>
          <input name="last_name" value="<?= e($old['last_name'] ?? '') ?>" required autocomplete="family-name">
          <?php if (!empty($errors['last_name'])): ?><div class="err"><?= e($errors['last_name']) ?></div><?php endif; ?>
        </div>
      </div>

      <div class="row">
        <div>
          <label>Дата рождения (YYYY-MM-DD)</label>
          <input name="dob" placeholder="2000-01-31" value="<?= e($old['dob'] ?? '') ?>" required>
          <?php if (!empty($errors['dob'])): ?><div class="err"><?= e($errors['dob']) ?></div><?php endif; ?>
        </div>
        <div>
          <label>Телефон (+374 00 000 000)</label>
          <input name="phone" placeholder="+374 00 000 000" value="<?= e($old['phone'] ?? '') ?>" required autocomplete="tel">
          <?php if (!empty($errors['phone'])): ?><div class="err"><?= e($errors['phone']) ?></div><?php endif; ?>
        </div>
      </div>

      <div class="row">
        <div>
          <label>Пароль (минимум 8 символов)</label>
          <input name="password" type="password" required autocomplete="new-password">
          <?php if (!empty($errors['password'])): ?><div class="err"><?= e($errors['password']) ?></div><?php endif; ?>
        </div>
        <div>
          <label>Подтверждение пароля</label>
          <input name="password_confirm" type="password" required autocomplete="new-password">
          <?php if (!empty($errors['password_confirm'])): ?><div class="err"><?= e($errors['password_confirm']) ?></div><?php endif; ?>
        </div>
      </div>

      <div class="actions">
        <button class="btn primary" type="submit">Создать аккаунт</button>
        <span class="muted">Уже есть аккаунт? <a href="/login">Войти</a></span>
      </div>
    </form>
  </div>
</div>
