<?php

declare(strict_types=1);

namespace App\Config;

use App\Support\Env;
use PDO;
use PDOException;

/**
 * PDO connection factory. Keeps one shared connection per request.
 */
final class Database
{
    private static ?PDO $connection = null;

    /**
     * @param array{host?: string, port?: string|int, database?: string} $config
     */
    public static function buildDsn(array $config): string
    {
        return sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'] ?? '127.0.0.1',
            (string) ($config['port'] ?? '3306'),
            $config['database'] ?? ''
        );
    }

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dsn = self::buildDsn([
            'host' => Env::get('DB_HOST', '127.0.0.1'),
            'port' => Env::get('DB_PORT', '3306'),
            'database' => Env::get('DB_DATABASE', 'blogy'),
        ]);

        try {
            self::$connection = new PDO(
                $dsn,
                (string) Env::get('DB_USERNAME', 'root'),
                (string) Env::get('DB_PASSWORD', ''),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }

        return self::$connection;
    }

    /**
     * Drops the cached connection (used in tests).
     */
    public static function reset(): void
    {
        self::$connection = null;
    }
}
