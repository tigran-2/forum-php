<?php
declare(strict_types=1);

use function App\Core\e;
use function App\Core\csrf_field;

/**
 * @var array $comment Comment data
 * @var array $old Previous input
 * @var array $errors Validation errors
 */
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Редактировать комментарий</h1>
    <form method="post" action="/comments/<?= (int)$comment['id'] ?>/update" novalidate>
      <?= csrf_field() ?>

      <label>Комментарий</label>
      <textarea name="body" required autofocus><?= e($old['body'] ?? '') ?></textarea>
      <?php if (!empty($errors['body'])): ?><div class="err"><?= e($errors['body']) ?></div><?php endif; ?>

      <div class="actions">
        <button class="btn primary" type="submit">Сохранить</button>
        <a class="btn" href="/topics/<?= (int)$comment['topic_id'] ?>">Отмена</a>
      </div>
    </form>
  </div>
</div>
