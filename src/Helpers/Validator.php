<?php
declare(strict_types=1);

namespace App\Helpers;

use DateTime;
use Throwable;

/**
 * Data validation helpers.
 */
class Validator
{
    /**
     * Normalize phone to +374XXXXXXXX format.
     */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($digits, '374') && strlen($digits) === 11) {
            return '+' . $digits;
        }
        return trim($phone);
    }

    /**
     * Validate Armenian phone format: +374 00 000 000
     */
    public static function validatePhoneAm(string $phone): bool
    {
        return (bool)preg_match('/^\+374\s?\d{2}\s?\d{3}\s?\d{3}$/', $phone);
    }

    /**
     * Validate name (letters, spaces, hyphens, min 2 chars).
     */
    public static function validateName(string $name): bool
    {
        $name = trim($name);
        if (mb_strlen($name) < 2) {
            return false;
        }
        return (bool)preg_match('/^[\p{L}]+([\s-][\p{L}]+)*$/u', $name);
    }

    /**
     * Calculate age from date of birth.
     */
    public static function ageFromDob(string $dob): ?int
    {
        $dob = trim($dob);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            return null;
        }
        try {
            $birth = new DateTime($dob);
            $today = new DateTime('today');
            return (int)$birth->diff($today)->y;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Validate email.
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password (min 8 chars).
     */
    public static function validatePassword(string $password): bool
    {
        return mb_strlen($password) >= 8;
    }

    /**
     * Get integer from input with validation.
     */
    public static function getInt(mixed $value, int $default = 0, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int
    {
        $value = filter_var($value, FILTER_VALIDATE_INT);
        if ($value === false) {
            return $default;
        }
        return max($min, min($max, $value));
    }

    /**
     * Get trimmed string from input.
     */
    public static function getString(mixed $value, string $default = ''): string
    {
        if (!is_string($value)) {
            return $default;
        }
        return trim($value);
    }
}
