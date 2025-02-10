<?php

class Database  
{  
    private static ?PDO $connection = null;  

    public static function connect(string $host, string $databaseName, string $username, string $password): void  
    {  
        if (self::$connection === null) {  
            try {
                $dsn = "mysql:host=$host;dbname=$databaseName";
                self::$connection = new PDO($dsn, $username, $password);  
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }  
    }  

    public static function getConnection(): PDO  
    {  
        if (self::$connection === null) {  
            throw new Exception("Database connection not established.");  
        }  
        return self::$connection;  
    }  
}

// Database credentials
$host = 'localhost';
$databaseName = 'users';
$username = 'phpmyadmin';
$password = '1234';

// Establish connection
Database::connect($host, $databaseName, $username, $password);

// Retrieve connection instance
$conn = Database::getConnection();
