<?php

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        require_once __DIR__ . '/../config/database.php';

        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            throw new RuntimeException('Database connection failed');
        }

        $this->conn->set_charset(DB_CHARSET);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function query(string $sql, string $types = '', array $params = [])
    {
        $stmt = $this->conn->prepare($sql);

        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        if (stripos(ltrim($sql), 'SELECT') === 0) {
            return $stmt->get_result();
        }

        return $stmt->affected_rows >= 0;
    }

    public function lastInsertId(): int
    {
        return $this->conn->insert_id;
    }
}
