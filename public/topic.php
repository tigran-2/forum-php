<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../app/forum.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(404); exit('Not found'); }

$topic = forum_get_topic($id);
if (!$topic) { http_response_code(404); exit('Not found'); }

$title = $topic['title'];
$comments = forum_list_comments($id);

$comment_errors = [];
$old_comment = '';

if (is_post()) {
    csrf_check();
    require_auth();
    $old_comment = (string)($_POST['body'] ?? '');

    $u = current_user();
    $res = forum_add_comment((int)$u['id'], $id, $old_comment);
    if ($res['ok']) {
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Комментарий добавлен.'];
        redirect('/topic.php?id=' . $id);
    } else {
        $comment_errors = $res['errors'] ?? ['body' => 'Ошибка.'];
    }
}

require __DIR__ . '/../partials/header.php';
?>
<div class="grid">
  <div class="card">
    <h1 class="h1"><?= e($topic['title']) ?></h1>
    <div class="muted">Автор: <?= e($topic['first_name'] . ' ' . $topic['last_name']) ?> · <?= e($topic['created_at']) ?></div>
    <div class="hr"></div>
    <div style="white-space: pre-wrap; line-height:1.5;"><?= e($topic['body']) ?></div>
  </div>

  <div class="card">
    <h2 class="h1" style="font-size:18px;">Комментарии (<?= (int)count($comments) ?>)</h2>

    <?php if (!$comments): ?>
      <div class="muted">Комментариев пока нет.</div>
    <?php else: ?>
      <?php foreach ($comments as $c): ?>
        <div style="padding:12px; border:1px solid var(--line); border-radius:14px; background: rgba(255,255,255,.02); margin-top:10px;">
          <div class="muted"><?= e($c['first_name'] . ' ' . $c['last_name']) ?> · <?= e($c['created_at']) ?></div>
          <div style="white-space: pre-wrap; margin-top:6px;"><?= e($c['body']) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="hr"></div>
    <?php if (current_user()): ?>
      <form method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <label>Добавить комментарий</label>
        <textarea name="body" required><?= e($old_comment) ?></textarea>
        <?php if (!empty($comment_errors['body'])): ?><div class="err"><?= e($comment_errors['body']) ?></div><?php endif; ?>
        <div style="margin-top:12px;">
          <button class="btn primary" type="submit">Отправить</button>
        </div>
      </form>
    <?php else: ?>
      <div class="muted">Чтобы написать комментарий, нужно <a href="/login.php">войти</a>.</div>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
