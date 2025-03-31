<?php
namespace App\Core;

class Database
{
    private $connection;
    private $statement;
    
    /**
     * Create a new Database instance
     *
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public function __construct($host, $database, $username, $password)
    {
        try {
            $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: {$e->getMessage()}");
        }
    }
    
    /**
     * Execute a query
     *
     * @param string $sql
     * @param array $params
     * @return self
     */
    public function query($sql, $params = [])
    {
        $this->statement = $this->connection->prepare($sql);
        $this->statement->execute($params);
        
        return $this;
    }
    
    /**
     * Fetch all records
     *
     * @return array
     */
    public function fetchAll()
    {
        return $this->statement->fetchAll();
    }
    
    /**
     * Fetch a single record
     *
     * @return array|false
     */
    public function fetch()
    {
        return $this->statement->fetch();
    }
    
    /**
     * Fetch a single column
     *
     * @param int $columnNumber
     * @return mixed
     */
    public function fetchColumn($columnNumber = 0)
    {
        return $this->statement->fetchColumn($columnNumber);
    }
    
    /**
     * Get the last inserted ID
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin a transaction
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit a transaction
     *
     * @return bool
     */
    public function commit()
    {
        return $this->connection->commit();
    }
    
    /**
     * Rollback a transaction
     *
     * @return bool
     */
    public function rollback()
    {
        return $this->connection->rollBack();
    }
    
    /**
     * Get the row count
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }
}