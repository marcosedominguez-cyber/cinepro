<?php

class Database
{
    private static ?PDO $conn = null;

    public static function connect(): PDO
    {
        if (self::$conn === null) {
            $host = '127.0.0.1';
            $port = '3306';
            $dbname = 'cinepython';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            self::$conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$conn;
    }
}