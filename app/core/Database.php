<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dbPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'database.sqlite';
            $dsn = 'sqlite:' . $dbPath;
            self::$connection = new PDO($dsn);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$connection->exec('PRAGMA foreign_keys = ON;');
            self::initialize(self::$connection);
        }

        return self::$connection;
    }

    private static function initialize(PDO $pdo): void
    {
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='accounts'")->fetch();
        if ($result) {
            return;
        }

        $schemaPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'schema.sql';
        $seedPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'seed.sql';

        if (!file_exists($schemaPath)) {
            throw new PDOException('Missing schema.sql');
        }

        $pdo->exec(file_get_contents($schemaPath));

        if (file_exists($seedPath)) {
            $pdo->exec(file_get_contents($seedPath));
        }
    }
}
