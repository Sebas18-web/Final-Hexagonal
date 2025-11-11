<?php

namespace App\Infrastructure\Persistence;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../../config/database.php';
            $socket = '/var/run/mysqld/mysqld.sock';

            try {
                // 🔍 Detecta si el socket de MySQL existe (entornos como WSL)
                if (file_exists($socket)) {
                    $dsn = "mysql:unix_socket={$socket};dbname={$config['database']};charset=utf8mb4";
                } else {
                    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
                }

                // 🔐 Crea la conexión PDO
                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new \RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
