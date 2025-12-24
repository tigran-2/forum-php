<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Helpers\Messages;

/**
 * Comment model with pagination.
 */
class Comment
{
    public const PER_PAGE = 20;

    /**
     * Get paginated comments for a topic.
     */
    /**
     * Get paginated comments for a topic.
     * 
     * @param int $topicId Topic ID
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array Pagination result
     */
    public static function paginate(int $topicId, int $page = 1, int $perPage = self::PER_PAGE): array
    {
        $pdo = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        // Get total count
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM comments WHERE topic_id = ?');
        $stmt->execute([$topicId]);
        $total = (int)$stmt->fetchColumn();

        // Get comments
        $stmt = $pdo->prepare("
            SELECT c.id, c.body, c.created_at, c.updated_at, c.user_id,
                   u.first_name, u.last_name
            FROM comments c
            JOIN users u ON u.id = c.user_id
            WHERE c.topic_id = ?
            ORDER BY c.created_at ASC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute([$topicId]);
        $comments = $stmt->fetchAll();

        return [
            'items' => $comments,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Get all comments for a topic (without pagination).
     */
    /**
     * Get all comments for a topic (without pagination).
     * 
     * @param int $topicId Topic ID
     * @return array List of comments
     */
    public static function forTopic(int $topicId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT c.id, c.body, c.created_at, c.updated_at, c.user_id,
                   u.first_name, u.last_name
            FROM comments c
            JOIN users u ON u.id = c.user_id
            WHERE c.topic_id = ?
            ORDER BY c.created_at ASC
        ');
        $stmt->execute([$topicId]);
        return $stmt->fetchAll();
    }

    /**
     * Find comment by ID.
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT c.id, c.topic_id, c.body, c.created_at, c.updated_at, c.user_id,
                   u.first_name, u.last_name
            FROM comments c
            JOIN users u ON u.id = c.user_id
            WHERE c.id = ?
            LIMIT 1
        ');
        $stmt->execute([$id]);
        $comment = $stmt->fetch();
        return $comment ?: null;
    }

    /**
     * Create a new comment.
     */
    /**
     * Create a new comment.
     * 
     * @param int $userId Author ID
     * @param int $topicId Topic ID
     * @param string $body Comment content
     * @return array Result ['ok' => bool, 'id' => int, 'errors' => array]
     */
    public static function create(int $userId, int $topicId, string $body): array
    {
        $body = trim($body);

        if ($body === '' || mb_strlen($body) < 2) {
            return ['ok' => false, 'errors' => ['body' => Messages::COMMENT_BODY_MIN]];
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO comments (topic_id, user_id, body) VALUES (?, ?, ?)');
        $stmt->execute([$topicId, $userId, $body]);

        return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
    }

    /**
     * Update a comment.
     */
    public static function update(int $id, string $body): array
    {
        $body = trim($body);

        if ($body === '' || mb_strlen($body) < 2) {
            return ['ok' => false, 'errors' => ['body' => Messages::COMMENT_BODY_MIN]];
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE comments SET body = ? WHERE id = ?');
        $stmt->execute([$body, $id]);

        return ['ok' => true];
    }

    /**
     * Delete a comment.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Check if user owns the comment.
     */
    public static function isOwner(int $commentId, int $userId): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT user_id FROM comments WHERE id = ?');
        $stmt->execute([$commentId]);
        $ownerId = $stmt->fetchColumn();
        return $ownerId === $userId;
    }
}
