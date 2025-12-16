<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Session management: CSRF tokens, flash messages, rate limiting.
 */
class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        session_set_cookie_params([
            'httponly' => true,
            'secure' => $secure,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;

        // Initialize CSRF token on first visit
        self::csrfToken();
    }

    /**
     * Get or generate CSRF token.
     */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    /**
     * Validate CSRF token (for POST requests).
     */
    public static function csrfCheck(): bool
    {
        $token = $_POST['csrf'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
            return false;
        }
        return true;
    }

    /**
     * Rotate CSRF token after successful use.
     */
    public static function csrfRotate(): void
    {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    /**
     * Get current user from session.
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Set current user in session.
     */
    public static function setUser(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
    }

    /**
     * Clear current user from session.
     */
    public static function clearUser(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    /**
     * Set flash message.
     */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
    }

    /**
     * Get and clear flash message.
     */
    public static function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Check rate limit for an action.
     * Returns true if allowed, false if rate limited.
     */
    public static function checkRateLimit(string $key, int $maxAttempts = 5, int $windowSeconds = 300): bool
    {
        $rateKey = 'rate_limit_' . $key;
        $attempts = $_SESSION[$rateKey] ?? ['count' => 0, 'first_attempt' => time()];

        // Reset window if expired
        if (time() - $attempts['first_attempt'] > $windowSeconds) {
            $attempts = ['count' => 0, 'first_attempt' => time()];
        }

        // Check if rate limited
        if ($attempts['count'] >= $maxAttempts) {
            return false;
        }

        // Increment attempts
        $attempts['count']++;
        $_SESSION[$rateKey] = $attempts;

        return true;
    }

    /**
     * Clear rate limit for a key (e.g., after successful login).
     */
    public static function clearRateLimit(string $key): void
    {
        unset($_SESSION['rate_limit_' . $key]);
    }

    /**
     * Get remaining time until rate limit resets.
     */
    public static function getRateLimitReset(string $key, int $windowSeconds = 300): int
    {
        $rateKey = 'rate_limit_' . $key;
        $attempts = $_SESSION[$rateKey] ?? null;
        
        if (!$attempts) {
            return 0;
        }

        $elapsed = time() - $attempts['first_attempt'];
        return max(0, $windowSeconds - $elapsed);
    }
}
