<?php
declare(strict_types=1);

use function App\Core\e;
use function App\Core\csrf_field;
use function App\Core\current_user;

$user = current_user();
?>
<div class="grid">
  <div class="card">
    <h1 class="h1"><?= e($topic['title']) ?></h1>
    <div class="muted">
      Автор: <a href="/profile/<?= (int)$topic['user_id'] ?>"><?= e($topic['first_name'] . ' ' . $topic['last_name']) ?></a>
      · <?= e($topic['created_at']) ?>
      <?php if (!empty($topic['updated_at']) && $topic['updated_at'] !== $topic['created_at']): ?>
        · <em>изменено <?= e($topic['updated_at']) ?></em>
      <?php endif; ?>
    </div>
    <div class="hr"></div>
    <div class="topic-body"><?= e($topic['body']) ?></div>

    <?php if ($is_owner): ?>
      <div class="actions">
        <a class="btn sm" href="/topics/<?= (int)$topic['id'] ?>/edit">Редактировать</a>
        <form method="post" action="/topics/<?= (int)$topic['id'] ?>/delete" style="margin:0;" onsubmit="return confirm('Удалить тему?')">
          <?= csrf_field() ?>
          <button class="btn sm danger" type="submit">Удалить</button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2 class="h1" style="font-size:18px;">Комментарии (<?= count($comments) ?>)</h2>

    <?php if (empty($comments)): ?>
      <div class="muted">Комментариев пока нет.</div>
    <?php else: ?>
      <?php foreach ($comments as $c): ?>
        <div class="comment">
          <div class="muted">
            <a href="/profile/<?= (int)$c['user_id'] ?>"><?= e($c['first_name'] . ' ' . $c['last_name']) ?></a>
            · <?= e($c['created_at']) ?>
            <?php if (!empty($c['updated_at']) && $c['updated_at'] !== $c['created_at']): ?>
              · <em>изменено</em>
            <?php endif; ?>
          </div>
          <div class="comment-body"><?= e($c['body']) ?></div>
          <?php if ($user && (int)$user['id'] === (int)$c['user_id']): ?>
            <div class="flex gap-1 mt-1">
              <a class="btn sm" href="/comments/<?= (int)$c['id'] ?>/edit">Редактировать</a>
              <form method="post" action="/comments/<?= (int)$c['id'] ?>/delete" style="margin:0;" onsubmit="return confirm('Удалить комментарий?')">
                <?= csrf_field() ?>
                <button class="btn sm danger" type="submit">Удалить</button>
              </form>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="hr"></div>

    <?php if ($user): ?>
      <form method="post" action="/topics/<?= (int)$topic['id'] ?>/comments" novalidate>
        <?= csrf_field() ?>
        <label>Добавить комментарий</label>
        <textarea name="body" required><?= e($old_comment ?? '') ?></textarea>
        <?php if (!empty($comment_errors['body'])): ?>
          <div class="err"><?= e($comment_errors['body']) ?></div>
        <?php endif; ?>
        <div class="actions">
          <button class="btn primary" type="submit">Отправить</button>
        </div>
      </form>
    <?php else: ?>
      <div class="muted">Чтобы написать комментарий, нужно <a href="/login">войти</a>.</div>
    <?php endif; ?>
  </div>
</div>
