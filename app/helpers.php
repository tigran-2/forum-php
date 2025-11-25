<?php
// app/helpers.php

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void {
    header("Location: {$path}");
    exit;
}

function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_auth(): void {
    if (!current_user()) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Войдите, чтобы продолжить.'];
        redirect('/login.php');
    }
}

function flash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void {
    if (!is_post()) return;
    $token = $_POST['csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(400);
        exit('Bad Request (CSRF)');
    }
}

function normalize_phone(string $phone): string {
    // Keep the value as-is (strict format), but trim whitespace.
    return trim($phone);
}

function validate_phone_am(string $phone): bool {
    // Формат: +374 00 000 000
    return (bool)preg_match('/^\+374\s\d{2}\s\d{3}\s\d{3}$/', $phone);
}

function validate_name(string $name): bool {
    // Только буквы (любой алфавит), пробелы и дефис; минимум 2 символа
    $name = trim($name);
    if (mb_strlen($name) < 2) return false;
    return (bool)preg_match('/^[\p{L}]+([\s-][\p{L}]+)*$/u', $name);
}

function age_from_dob(string $dob): ?int {
    // $dob is expected in YYYY-MM-DD format.
    $dob = trim($dob);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) return null;
    try {
        $birth = new DateTime($dob);
        $today = new DateTime('today');
        return (int)$birth->diff($today)->y;
    } catch (Throwable $e) {
        return null;
    }
}
