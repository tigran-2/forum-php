<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Helpers\Validator;
use App\Helpers\Messages;
use PDO;

/**
 * User model.
 */
class User
{
    /**
     * Find user by ID.
     */
    /**
     * Find user by ID.
     * 
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT id, email, first_name, last_name, dob, phone, avatar_url, created_at, updated_at
            FROM users WHERE id = ? LIMIT 1
        ');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by email.
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('
            SELECT id, email, first_name, last_name, password_hash
            FROM users WHERE email = ? LIMIT 1
        ');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Create a new user.
     * Returns ['ok' => true] on success or ['ok' => false, 'errors' => [...]] on failure.
     */
    /**
     * Create a new user.
     * 
     * Validates input data before creation.
     * Returns ['ok' => true, 'id' => new_id] on success,
     * or ['ok' => false, 'errors' => [...]] on failure.
     * 
     * @param array $data Input data (email, password, etc.)
     * @return array Result array
     */
    public static function create(array $data): array
    {
        $errors = self::validate($data);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $email = trim($data['email']);
        $first = trim($data['first_name']);
        $last = trim($data['last_name']);
        $dob = trim($data['dob']);
        $phone = Validator::normalizePhone($data['phone']);
        $pass = $data['password'];

        $pdo = Database::getInstance();

        // Check email uniqueness
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['ok' => false, 'errors' => ['email' => Messages::EMAIL_EXISTS]];
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('
            INSERT INTO users (email, first_name, last_name, dob, phone, password_hash)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$email, $first, $last, $dob, $phone, $hash]);

        return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
    }

    /**
     * Update user profile.
     */
    /**
     * Update user profile.
     * 
     * Validates input (name, phone) before updating.
     * 
     * @param int $id User ID
     * @param array $data Input data to update
     * @return array Result array ['ok' => bool, 'errors' => array]
     */
    public static function update(int $id, array $data): array
    {
        $errors = [];

        $first = trim($data['first_name'] ?? '');
        $last = trim($data['last_name'] ?? '');
        $phone = isset($data['phone']) ? Validator::normalizePhone($data['phone']) : null;

        if (!Validator::validateName($first)) {
            $errors['first_name'] = Messages::FIRST_NAME_INVALID;
        }
        if (!Validator::validateName($last)) {
            $errors['last_name'] = Messages::LAST_NAME_INVALID;
        }
        if ($phone !== null && !Validator::validatePhoneAm($phone)) {
            $errors['phone'] = Messages::PHONE_INVALID;
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $pdo = Database::getInstance();
        $sql = 'UPDATE users SET first_name = ?, last_name = ?';
        $params = [$first, $last];

        if ($phone !== null) {
            $sql .= ', phone = ?';
            $params[] = $phone;
        }

        $sql .= ' WHERE id = ?';
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return ['ok' => true];
    }

    /**
     * Verify password.
     */
    public static function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash']);
    }

    /**
     * Rehash password if needed.
     */
    public static function rehashIfNeeded(int $id, string $password, string $currentHash): void
    {
        if (password_needs_rehash($currentHash, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $stmt->execute([$newHash, $id]);
        }
    }

    /**
     * Validate registration data.
     */
    private static function validate(array $data): array
    {
        $errors = [];

        $email = trim($data['email'] ?? '');
        $first = trim($data['first_name'] ?? '');
        $last = trim($data['last_name'] ?? '');
        $dob = trim($data['dob'] ?? '');
        $phone = Validator::normalizePhone($data['phone'] ?? '');
        $pass = $data['password'] ?? '';
        $pass2 = $data['password_confirm'] ?? '';

        if (!Validator::validateEmail($email)) {
            $errors['email'] = Messages::EMAIL_REQUIRED;
        }
        if (!Validator::validateName($first)) {
            $errors['first_name'] = Messages::FIRST_NAME_INVALID;
        }
        if (!Validator::validateName($last)) {
            $errors['last_name'] = Messages::LAST_NAME_INVALID;
        }

        $age = Validator::ageFromDob($dob);
        if ($age === null) {
            $errors['dob'] = Messages::DOB_FORMAT;
        } elseif ($age < 18) {
            $errors['dob'] = Messages::DOB_AGE_LIMIT;
        }

        if (!Validator::validatePhoneAm($phone)) {
            $errors['phone'] = Messages::PHONE_INVALID;
        }

        if (!Validator::validatePassword($pass)) {
            $errors['password'] = Messages::PASSWORD_MIN_LENGTH;
        }
        if ($pass !== $pass2) {
            $errors['password_confirm'] = Messages::PASSWORD_MISMATCH;
        }

        return $errors;
    }

    /**
     * Get user's topic count.
     */
    public static function getTopicCount(int $userId): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM topics WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get user's comment count.
     */
    public static function getCommentCount(int $userId): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM comments WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
