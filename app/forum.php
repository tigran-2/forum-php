<?php
// app/forum.php
require_once __DIR__ . '/db.php';

function forum_list_topics(): array {
    $pdo = db();
    return $pdo->query('
        SELECT t.id, t.title, t.created_at, u.first_name, u.last_name
        FROM topics t
        JOIN users u ON u.id = t.user_id
        ORDER BY t.created_at DESC
    ')->fetchAll();
}

function forum_get_topic(int $id): ?array {
    $pdo = db();
    $stmt = $pdo->prepare('
        SELECT t.id, t.title, t.body, t.created_at, t.user_id, u.first_name, u.last_name
        FROM topics t
        JOIN users u ON u.id = t.user_id
        WHERE t.id = ?
        LIMIT 1
    ');
    $stmt->execute([$id]);
    $t = $stmt->fetch();
    return $t ?: null;
}

function forum_list_comments(int $topic_id): array {
    $pdo = db();
    $stmt = $pdo->prepare('
        SELECT c.id, c.body, c.created_at, u.first_name, u.last_name
        FROM comments c
        JOIN users u ON u.id = c.user_id
        WHERE c.topic_id = ?
        ORDER BY c.created_at ASC
    ');
    $stmt->execute([$topic_id]);
    return $stmt->fetchAll();
}

function forum_create_topic(int $user_id, string $title, string $body): array {
    $errors = [];
    $title = trim($title);
    $body  = trim($body);

    if ($title === '' || mb_strlen($title) < 3) $errors['title'] = 'Заголовок должен быть не короче 3 символов.';
    if ($body === '' || mb_strlen($body) < 10) $errors['body'] = 'Текст темы должен быть не короче 10 символов.';
    if ($errors) return ['ok' => false, 'errors' => $errors];

    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO topics (user_id, title, body) VALUES (?, ?, ?)');
    $stmt->execute([$user_id, $title, $body]);

    return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
}

function forum_add_comment(int $user_id, int $topic_id, string $body): array {
    $body = trim($body);
    if ($body === '' || mb_strlen($body) < 2) {
        return ['ok' => false, 'errors' => ['body' => 'Комментарий слишком короткий.']];
    }

    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO comments (topic_id, user_id, body) VALUES (?, ?, ?)');
    $stmt->execute([$topic_id, $user_id, $body]);

    return ['ok' => true];
}
