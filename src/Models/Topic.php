<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Helpers\Messages;

/**
 * Topic model with pagination and search.
 */
class Topic
{
    public const PER_PAGE = 10;

    /**
     * Get paginated topics with optional search.
     */
    public static function paginate(int $page = 1, int $perPage = self::PER_PAGE, ?string $search = null): array
    {
        $pdo = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];

        if ($search !== null && trim($search) !== '') {
            $where = 'WHERE t.title LIKE ? OR t.body LIKE ?';
            $searchTerm = '%' . trim($search) . '%';
            $params = [$searchTerm, $searchTerm];
        }

        // Get total count
        $countSql = "SELECT COUNT(*) FROM topics t {$where}";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        // Get topics
        $sql = "
            SELECT t.id, t.title, t.created_at, t.updated_at, u.id AS user_id, u.first_name, u.last_name,
                   (SELECT COUNT(*) FROM comments WHERE topic_id = t.id) AS comment_count
            FROM topics t
            JOIN users u ON u.id = t.user_id
            {$where}
            ORDER BY t.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $topics = $stmt->fetchAll();

        return [
            'items' => $topics,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Find topic by ID.
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT t.id, t.title, t.body, t.created_at, t.updated_at, t.user_id,
                   u.first_name, u.last_name
            FROM topics t
            JOIN users u ON u.id = t.user_id
            WHERE t.id = ?
            LIMIT 1
        ');
        $stmt->execute([$id]);
        $topic = $stmt->fetch();
        return $topic ?: null;
    }

    /**
     * Create a new topic.
     */
    public static function create(int $userId, string $title, string $body): array
    {
        $title = trim($title);
        $body = trim($body);
        $errors = [];

        if ($title === '' || mb_strlen($title) < 3) {
            $errors['title'] = Messages::TOPIC_TITLE_MIN;
        }
        if ($body === '' || mb_strlen($body) < 10) {
            $errors['body'] = Messages::TOPIC_BODY_MIN;
        }
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO topics (user_id, title, body) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $title, $body]);

        return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
    }

    /**
     * Update a topic.
     */
    public static function update(int $id, string $title, string $body): array
    {
        $title = trim($title);
        $body = trim($body);
        $errors = [];

        if ($title === '' || mb_strlen($title) < 3) {
            $errors['title'] = Messages::TOPIC_TITLE_MIN;
        }
        if ($body === '' || mb_strlen($body) < 10) {
            $errors['body'] = Messages::TOPIC_BODY_MIN;
        }
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE topics SET title = ?, body = ? WHERE id = ?');
        $stmt->execute([$title, $body, $id]);

        return ['ok' => true];
    }

    /**
     * Delete a topic.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('DELETE FROM topics WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Check if user owns the topic.
     */
    public static function isOwner(int $topicId, int $userId): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT user_id FROM topics WHERE id = ?');
        $stmt->execute([$topicId]);
        $ownerId = $stmt->fetchColumn();
        return $ownerId === $userId;
    }
}
