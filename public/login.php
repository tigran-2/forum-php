<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../app/auth.php';

$title = 'Вход';
$error = null;
$old_email = '';

if (is_post()) {
    csrf_check();
    $old_email = (string)($_POST['email'] ?? '');
    $res = auth_login((string)($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''));
    if ($res['ok']) {
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Вы вошли в систему.'];
        redirect('/index.php');
    } else {
        $error = $res['error'] ?? 'Ошибка входа.';
    }
}

require __DIR__ . '/../partials/header.php';
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Вход</h1>
    <form method="post" novalidate>
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

      <label>Email</label>
      <input name="email" type="email" value="<?= e($old_email) ?>" required>

      <label>Пароль</label>
      <input name="password" type="password" required>

      <?php if ($error): ?><div class="err" style="margin-top:10px;"><?= e($error) ?></div><?php endif; ?>

      <div style="margin-top:12px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <button class="btn primary" type="submit">Войти</button>
        <span class="muted">Нет аккаунта? <a href="/register.php">Регистрация</a></span>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
