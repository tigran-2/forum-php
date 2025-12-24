<?php
declare(strict_types=1);

use function App\Core\e;
use function App\Core\url;
use function App\Core\current_user;
use function App\Core\partial;

$user = current_user();

/**
 * @var array $topics List of topics
 * @var array $pagination Pagination data
 * @var string|null $search Search query
 */
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Темы</h1>
    <div class="muted">Создавать темы и писать комментарии могут только зарегистрированные пользователи.</div>

    <?php if ($search || true): ?>
    <form class="search-form mt-2" method="get" action="/">
      <input type="text" name="q" value="<?= e($search ?? '') ?>" placeholder="Поиск по темам...">
      <button class="btn primary" type="submit">Найти</button>
      <?php if ($search): ?>
        <a class="btn" href="/">Сбросить</a>
      <?php endif; ?>
    </form>
    <?php endif; ?>
  </div>

  <?php if (empty($topics)): ?>
    <div class="card">
      <div class="muted">
        <?php if ($search): ?>
          По запросу "<?= e($search) ?>" ничего не найдено.
        <?php else: ?>
          Пока нет тем. <?= $user ? 'Создайте первую.' : 'Войдите и создайте первую тему.' ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php foreach ($topics as $t): ?>
    <div class="card">
      <p class="topic-title"><a href="/topics/<?= (int)$t['id'] ?>"><?= e($t['title']) ?></a></p>
      <div class="muted topic-meta">
        Автор: <a href="/profile/<?= (int)$t['user_id'] ?>"><?= e($t['first_name'] . ' ' . $t['last_name']) ?></a> 
        · <?= e($t['created_at']) ?>
        · <?= (int)$t['comment_count'] ?> <?= (int)$t['comment_count'] === 1 ? 'комментарий' : 'комментариев' ?>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if ($pagination['total_pages'] > 1): ?>
    <?= partial('pagination', ['pagination' => $pagination, 'search' => $search]) ?>
  <?php endif; ?>
</div>
