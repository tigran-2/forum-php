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
    /** @var PDO|null The PDO instance */
    private static ?PDO $instance = null;

    private function __construct() {}

    /**
     * Get the PDO database connection.
     * Creates a new connection if one doesn't exist.
     * 
     * @return PDO
     * @throws PDOException If connection fails
     */
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

    /**
     * Load database configuration from the config file.
     * 
     * @return array
     */
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
