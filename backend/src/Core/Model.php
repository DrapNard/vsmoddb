<?php
namespace App\Core;

abstract class Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table;
    
    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [];
    
    /**
     * Get the database instance
     *
     * @return Database
     */
    protected function db()
    {
        return App::get('db');
    }
    
    /**
     * Find a record by its primary key
     *
     * @param mixed $id
     * @return array|null
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $result = $this->db()->query($sql, [$id])->fetch();
        
        return $result ?: null;
    }
    
    /**
     * Get all records
     *
     * @return array
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db()->query($sql)->fetchAll();
    }
    
    /**
     * Create a new record
     *
     * @param array $data
     * @return int|bool
     */
    public function create(array $data)
    {
        // Filter data to only include fillable attributes
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($data)) {
            return false;
        }
        
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db()->query($sql, array_values($data));
        
        return $this->db()->lastInsertId();
    }
    
    /**
     * Update a record
     *
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        // Filter data to only include fillable attributes
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($data)) {
            return false;
        }
        
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        $this->db()->query($sql, array_merge(array_values($data), [$id]));
        
        return $this->db()->rowCount() > 0;
    }
    
    /**
     * Delete a record
     *
     * @param mixed $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $this->db()->query($sql, [$id]);
        
        return $this->db()->rowCount() > 0;
    }
    
    /**
     * Find records by a specific field
     *
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function findBy($field, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = ?";
        return $this->db()->query($sql, [$value])->fetchAll();
    }
    
    /**
     * Find a single record by a specific field
     *
     * @param string $field
     * @param mixed $value
     * @return array|null
     */
    public function findOneBy($field, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = ?";
        $result = $this->db()->query($sql, [$value])->fetch();
        
        return $result ?: null;
    }
}