<?php

namespace App\Config;

use PDO;
use PDOException;
use MongoDB\Client;

class Database
{
    private static ?PDO $postgres = null;
    private static ?Client $mongo = null;

    public static function init(): void
    {
        self::getPostgres();
        self::getMongo();
    }

    public static function getPostgres(): PDO
    {
        if (self::$postgres === null) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $_ENV['PG_HOST'],
                $_ENV['PG_PORT'],
                $_ENV['PG_DATABASE']
            );

            try {
                self::$postgres = new PDO(
                    $dsn,
                    $_ENV['PG_USER'],
                    $_ENV['PG_PASSWORD'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                die("PostgreSQL connection failed: " . $e->getMessage());
            }
        }

        return self::$postgres;
    }

    public static function getMongo(): Client
    {
        if (self::$mongo === null) {
            $uri = sprintf(
                'mongodb://%s:%s',
                $_ENV['MONGO_HOST'],
                $_ENV['MONGO_PORT']
            );

            try {
                self::$mongo = new Client($uri);
            } catch (\Exception $e) {
                die("MongoDB connection failed: " . $e->getMessage());
            }
        }

        return self::$mongo;
    }

    public static function getMongoDatabase(): \MongoDB\Database
    {
        return self::getMongo()->selectDatabase($_ENV['MONGO_DATABASE']);
    }
}