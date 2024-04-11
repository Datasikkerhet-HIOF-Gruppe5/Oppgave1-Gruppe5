<?php
header("Content-Security-Policy: upgrade-insecure-requests");
class Database
{
    private static $pdo;

    private function __construct() {}

    public static function getInstance()
    {
        if (!self::$pdo) {
            self::$pdo = self::createConnection();
        }
        return self::$pdo;
    }

    private static function createConnection()
    {
        $host = 'localhost';
        $db   = 'dbname';
        $user = 'username';
        $pass = 'password';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            return new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}

