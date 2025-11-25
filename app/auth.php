<?php
// app/auth.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function auth_register(array $data): array {
    $errors = [];

    $email = trim((string)($data['email'] ?? ''));
    $first = trim((string)($data['first_name'] ?? ''));
    $last  = trim((string)($data['last_name'] ?? ''));
    $dob   = trim((string)($data['dob'] ?? ''));
    $phone = normalize_phone((string)($data['phone'] ?? ''));
    $pass  = (string)($data['password'] ?? '');
    $pass2 = (string)($data['password_confirm'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Укажите корректный email.';
    }
    if (!validate_name($first)) {
        $errors['first_name'] = 'Имя должно содержать только буквы (пробел и дефис допускаются).';
    }
    if (!validate_name($last)) {
        $errors['last_name'] = 'Фамилия должна содержать только буквы (пробел и дефис допускаются).';
    }

    $age = age_from_dob($dob);
    if ($age === null) {
        $errors['dob'] = 'Дата рождения должна быть в формате YYYY-MM-DD.';
    } elseif ($age < 18) {
        $errors['dob'] = 'Регистрация доступна с 18 лет.';
    }

    if (!validate_phone_am($phone)) {
        $errors['phone'] = 'Телефон должен быть в формате +374 00 000 000.';
    }

    if (mb_strlen($pass) < 8) {
        $errors['password'] = 'Пароль должен быть не короче 8 символов.';
    }
    if ($pass !== $pass2) {
        $errors['password_confirm'] = 'Пароль и подтверждение не совпадают.';
    }

    if ($errors) return ['ok' => false, 'errors' => $errors];

    $pdo = db();

    // Check email uniqueness.
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['ok' => false, 'errors' => ['email' => 'Этот email уже зарегистрирован.']];
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('
        INSERT INTO users (email, first_name, last_name, dob, phone, password_hash)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$email, $first, $last, $dob, $phone, $hash]);

    return ['ok' => true];
}

function auth_login(string $email, string $password): array {
    $email = trim($email);
    if ($email === '' || $password === '') {
        return ['ok' => false, 'error' => 'Введите email и пароль.'];
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, email, first_name, last_name, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['ok' => false, 'error' => 'Неверный email или пароль.'];
    }

    // Minimal protection against session fixation.
    session_regenerate_id(true);

    unset($user['password_hash']);
    $_SESSION['user'] = $user;

    return ['ok' => true];
}

function auth_logout(): void {
    unset($_SESSION['user']);
    session_regenerate_id(true);
}
