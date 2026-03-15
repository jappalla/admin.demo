<?php
declare(strict_types=1);

namespace App\Support;

use PDO;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $defaultConnection = (string) config('database.default', 'mysql');
        $connectionConfig = config('database.connections.' . $defaultConnection, []);
        if (!is_array($connectionConfig) || $connectionConfig === []) {
            throw new RuntimeException('Missing database connection configuration.');
        }

        $dsn = (string) ($connectionConfig['dsn'] ?? '');
        if ($dsn === '') {
            throw new RuntimeException('Missing database DSN.');
        }

        $username = (string) ($connectionConfig['username'] ?? '');
        $password = (string) ($connectionConfig['password'] ?? '');

        self::$connection = new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return self::$connection;
    }

    public static function serverConnection(): PDO
    {
        $defaultConnection = (string) config('database.default', 'mysql');
        $connectionConfig = config('database.connections.' . $defaultConnection, []);
        if (!is_array($connectionConfig) || $connectionConfig === []) {
            throw new RuntimeException('Missing database connection configuration.');
        }

        $driver = (string) ($connectionConfig['driver'] ?? 'mysql');
        $host = (string) ($connectionConfig['host'] ?? '127.0.0.1');
        $port = (string) ($connectionConfig['port'] ?? '3306');
        $charset = (string) ($connectionConfig['charset'] ?? 'utf8mb4');
        $username = (string) ($connectionConfig['username'] ?? '');
        $password = (string) ($connectionConfig['password'] ?? '');

        $dsn = sprintf('%s:host=%s;port=%s;charset=%s', $driver, $host, $port, $charset);

        return new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
