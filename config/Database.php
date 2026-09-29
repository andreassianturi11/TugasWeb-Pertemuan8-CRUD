<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private string $host = 'localhost';
    private string $dbname = 'inventaris_db';
    private string $user = 'root';
    private string $pass = '';
    private string $charset = 'utf8mb4';

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            die('Koneksi database gagal. Periksa Apache, MySQL, dan konfigurasi database.');
        }
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception('Tidak dapat melakukan unserialize singleton Database.');
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
