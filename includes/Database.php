<?php
class Database
{
    private mysqli $connection;

    public function __construct()
    {
        require_once __DIR__ . '/../config.php';
        $this->connection = getDbConnection();
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }
}
?>
