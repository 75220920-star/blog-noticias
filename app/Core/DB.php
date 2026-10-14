<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class DB
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (!self::$connection) {
            $config = $GLOBALS['config']['db'];
            self::$connection = new PDO($config['dsn'], $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            self::$connection->exec("SET time_zone = '-05:00'");
        }
        return self::$connection;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $statement = self::connection()->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public static function audit(string $action, string $entity, ?int $id = null): void
    {
        self::query(
            'INSERT INTO audit_log (user_id, action, entity, entity_id, ip_address) VALUES (?, ?, ?, ?, ?)',
            [$_SESSION['user_id'] ?? null, $action, $entity, $id, substr($_SERVER['REMOTE_ADDR'] ?? 'CLI', 0, 45)]
        );
    }
}
