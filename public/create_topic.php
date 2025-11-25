<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../app/forum.php';

require_auth();

$title = 'Создать тему';
$errors = [];
$old = ['title' => '', 'body' => ''];

if (is_post()) {
    csrf_check();
    $old['title'] = (string)($_POST['title'] ?? '');
    $old['body']  = (string)($_POST['body'] ?? '');

    $u = current_user();
    $res = forum_create_topic((int)$u['id'], $old['title'], $old['body']);
    if ($res['ok']) {
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Тема создана.'];
        redirect('/topic.php?id=' . (int)$res['id']);
    } else {
        $errors = $res['errors'] ?? ['form' => 'Ошибка создания темы.'];
    }
}

require __DIR__ . '/../partials/header.php';
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Создать тему</h1>
    <form method="post" novalidate>
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

      <label>Заголовок</label>
      <input name="title" value="<?= e($old['title']) ?>" required>
      <?php if (!empty($errors['title'])): ?><div class="err"><?= e($errors['title']) ?></div><?php endif; ?>

      <label>Текст</label>
      <textarea name="body" required><?= e($old['body']) ?></textarea>
      <?php if (!empty($errors['body'])): ?><div class="err"><?= e($errors['body']) ?></div><?php endif; ?>

      <div style="margin-top:12px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <button class="btn primary" type="submit">Опубликовать</button>
        <a class="btn" href="/index.php">Отмена</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
