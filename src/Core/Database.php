<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database singleton wrapper for PDO.
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = self::loadConfig();
            $db = $config['db'];

            try {
                self::$instance = new PDO(
                    $db['dsn'],
                    $db['user'],
                    $db['pass'],
                    $db['options']
                );
            } catch (PDOException $e) {
                throw new PDOException('Database connection failed: ' . $e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    private static function loadConfig(): array
    {
        static $config = null;
        if ($config === null) {
            $config = require dirname(__DIR__, 2) . '/config/config.php';
        }
        return $config;
    }

    /**
     * Reset connection (useful for testing).
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
