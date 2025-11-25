<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../app/auth.php';

$title = 'Регистрация';
$errors = [];
$old = [
    'email' => '',
    'first_name' => '',
    'last_name' => '',
    'dob' => '',
    'phone' => ''
];

if (is_post()) {
    csrf_check();
    $old = array_merge($old, [
        'email' => (string)($_POST['email'] ?? ''),
        'first_name' => (string)($_POST['first_name'] ?? ''),
        'last_name' => (string)($_POST['last_name'] ?? ''),
        'dob' => (string)($_POST['dob'] ?? ''),
        'phone' => (string)($_POST['phone'] ?? ''),
    ]);

    $res = auth_register($_POST);
    if ($res['ok']) {
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Регистрация успешна. Теперь войдите.'];
        redirect('/login.php');
    } else {
        $errors = $res['errors'] ?? ['form' => 'Ошибка регистрации.'];
    }
}

require __DIR__ . '/../partials/header.php';
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">Регистрация</h1>
    <form method="post" novalidate>
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

      <label>Email</label>
      <input name="email" type="email" value="<?= e($old['email']) ?>" required>
      <?php if (!empty($errors['email'])): ?><div class="err"><?= e($errors['email']) ?></div><?php endif; ?>

      <div class="row">
        <div>
          <label>Имя</label>
          <input name="first_name" value="<?= e($old['first_name']) ?>" required>
          <?php if (!empty($errors['first_name'])): ?><div class="err"><?= e($errors['first_name']) ?></div><?php endif; ?>
        </div>
        <div>
          <label>Фамилия</label>
          <input name="last_name" value="<?= e($old['last_name']) ?>" required>
          <?php if (!empty($errors['last_name'])): ?><div class="err"><?= e($errors['last_name']) ?></div><?php endif; ?>
        </div>
      </div>

      <div class="row">
        <div>
          <label>Дата рождения (YYYY-MM-DD)</label>
          <input name="dob" placeholder="2000-01-31" value="<?= e($old['dob']) ?>" required>
          <?php if (!empty($errors['dob'])): ?><div class="err"><?= e($errors['dob']) ?></div><?php endif; ?>
        </div>
        <div>
          <label>Телефон (+374 00 000 000)</label>
          <input name="phone" placeholder="+374 00 000 000" value="<?= e($old['phone']) ?>" required>
          <?php if (!empty($errors['phone'])): ?><div class="err"><?= e($errors['phone']) ?></div><?php endif; ?>
        </div>
      </div>

      <div class="row">
        <div>
          <label>Пароль (минимум 8 символов)</label>
          <input name="password" type="password" required>
          <?php if (!empty($errors['password'])): ?><div class="err"><?= e($errors['password']) ?></div><?php endif; ?>
        </div>
        <div>
          <label>Подтверждение пароля</label>
          <input name="password_confirm" type="password" required>
          <?php if (!empty($errors['password_confirm'])): ?><div class="err"><?= e($errors['password_confirm']) ?></div><?php endif; ?>
        </div>
      </div>

      <div style="margin-top:12px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <button class="btn primary" type="submit">Создать аккаунт</button>
        <span class="muted">Уже есть аккаунт? <a href="/login.php">Войти</a></span>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
