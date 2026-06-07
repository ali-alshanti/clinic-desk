<?php

abstract class BaseModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function execute(string $sql, string $types = '', array $params = [])
    {
        return $this->db->query($sql, $types, $params);
    }
}
