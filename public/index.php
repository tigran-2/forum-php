<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../app/forum.php';

$title = 'Темы';
$topics = forum_list_topics();

require __DIR__ . '/../partials/header.php';
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Темы</h1>
    <div class="muted">Создавать темы и писать комментарии могут только зарегистрированные пользователи.</div>
  </div>

  <?php if (!$topics): ?>
    <div class="card">
      <div class="muted">Пока нет тем. <?= current_user() ? 'Создайте первую.' : 'Войдите и создайте первую тему.' ?></div>
    </div>
  <?php endif; ?>

  <?php foreach ($topics as $t): ?>
    <div class="card">
      <p class="topic-title"><a href="/topic.php?id=<?= (int)$t['id'] ?>"><?= e($t['title']) ?></a></p>
      <div class="muted topic-meta">Автор: <?= e($t['first_name'] . ' ' . $t['last_name']) ?> · <?= e($t['created_at']) ?></div>
    </div>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
