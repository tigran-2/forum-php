<?php
declare(strict_types=1);

use function App\Core\e;
use function App\Core\csrf_field;
use function App\Core\current_user;

$user = current_user();
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Редактировать профиль</h1>
    <form method="post" action="/profile/update" novalidate>
      <?= csrf_field() ?>

      <div class="row">
        <div>
          <label>Имя</label>
          <input name="first_name" value="<?= e($old['first_name'] ?? '') ?>" required>
          <?php if (!empty($errors['first_name'])): ?><div class="err"><?= e($errors['first_name']) ?></div><?php endif; ?>
        </div>
        <div>
          <label>Фамилия</label>
          <input name="last_name" value="<?= e($old['last_name'] ?? '') ?>" required>
          <?php if (!empty($errors['last_name'])): ?><div class="err"><?= e($errors['last_name']) ?></div><?php endif; ?>
        </div>
      </div>

      <label>Телефон</label>
      <input name="phone" value="<?= e($old['phone'] ?? '') ?>" placeholder="+374 00 000 000">
      <?php if (!empty($errors['phone'])): ?><div class="err"><?= e($errors['phone']) ?></div><?php endif; ?>

      <div class="actions">
        <button class="btn primary" type="submit">Сохранить</button>
        <a class="btn" href="/profile/<?= (int)$user['id'] ?>">Отмена</a>
      </div>
    </form>
  </div>
</div>
