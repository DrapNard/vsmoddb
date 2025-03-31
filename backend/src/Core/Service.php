<?php
namespace App\Core;

class Service
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database(
            DB_HOST,
            DB_NAME,
            DB_USER,
            DB_PASS
        );
    }

    /**
     * Begin a database transaction
     */
    protected function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit a database transaction
     */
    protected function commit()
    {
        return $this->db->commit();
    }

    /**
     * Rollback a database transaction
     */
    protected function rollBack()
    {
        return $this->db->rollBack();
    }

    /**
     * Execute a database query
     *
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    protected function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }

    /**
     * Get the last inserted ID
     *
     * @return int
     */
    protected function lastInsertId()
    {
        return $this->db->lastInsertId();
    }
}