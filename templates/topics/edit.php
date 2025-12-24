<?php
declare(strict_types=1);

use function App\Core\e;
use function App\Core\csrf_field;

/**
 * @var array $topic Topic data
 * @var array $old Previous input
 * @var array $errors Validation errors
 */
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Редактировать тему</h1>
    <form method="post" action="/topics/<?= (int)$topic['id'] ?>/update" novalidate>
      <?= csrf_field() ?>

      <label>Заголовок</label>
      <input name="title" value="<?= e($old['title'] ?? '') ?>" required autofocus>
      <?php if (!empty($errors['title'])): ?><div class="err"><?= e($errors['title']) ?></div><?php endif; ?>

      <label>Текст</label>
      <textarea name="body" required><?= e($old['body'] ?? '') ?></textarea>
      <?php if (!empty($errors['body'])): ?><div class="err"><?= e($errors['body']) ?></div><?php endif; ?>

      <div class="actions">
        <button class="btn primary" type="submit">Сохранить</button>
        <a class="btn" href="/topics/<?= (int)$topic['id'] ?>">Отмена</a>
      </div>
    </form>
  </div>
</div>
