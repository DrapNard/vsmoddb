<?php
namespace App\Models;

use App\Core\Model;

class Mod extends Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'mod';
    
    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'assetid';
    
    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'userid',
        'urlalias',
        'downloads',
        'created'
    ];
    
    /**
     * Get all mods with their users
     *
     * @return array
     */
    public function getAllWithUsers()
    {
        $sql = "SELECT m.*, u.username 
               FROM mod m 
               JOIN user u ON m.userid = u.userid 
               ORDER BY m.created DESC";
        
        return $this->db()->query($sql)->fetchAll();
    }
    
    /**
     * Get a mod with its user
     *
     * @param int $id
     * @return array|null
     */
    public function getWithUser($id)
    {
        $sql = "SELECT m.*, u.username, u.displayname 
               FROM mod m 
               JOIN user u ON m.userid = u.userid 
               WHERE m.assetid = ?";
        
        $result = $this->db()->query($sql, [$id])->fetch();
        
        return $result ?: null;
    }
    
    /**
     * Get releases for a mod
     *
     * @param int $modId
     * @return array
     */
    public function getReleases($modId)
    {
        $sql = "SELECT r.* 
               FROM release r 
               WHERE r.modid = ? 
               ORDER BY r.created DESC";
        
        return $this->db()->query($sql, [$modId])->fetchAll();
    }
    
    /**
     * Get comments for a mod
     *
     * @param int $modId
     * @return array
     */
    public function getComments($modId)
    {
        $sql = "SELECT c.*, u.username, u.displayname 
               FROM comment c 
               JOIN user u ON c.userid = u.userid 
               WHERE c.assetid = ? 
               ORDER BY c.created ASC";
        
        return $this->db()->query($sql, [$modId])->fetchAll();
    }
    
    /**
     * Increment download count
     *
     * @param int $modId
     * @return bool
     */
    public function incrementDownloads($modId)
    {
        $sql = "UPDATE mod SET downloads = downloads + 1 WHERE assetid = ?";
        $this->db()->query($sql, [$modId]);
        
        return $this->db()->rowCount() > 0;
    }
    
    /**
     * Get trending mods
     *
     * @param int $limit
     * @return array
     */
    public function getTrending($limit = 10)
    {
        $sql = "SELECT m.*, u.username 
               FROM mod m 
               JOIN user u ON m.userid = u.userid 
               ORDER BY m.downloads DESC 
               LIMIT ?";
        
        return $this->db()->query($sql, [$limit])->fetchAll();
    }
    
    /**
     * Get latest mods
     *
     * @param int $limit
     * @return array
     */
    public function getLatest($limit = 10)
    {
        $sql = "SELECT m.*, u.username 
               FROM mod m 
               JOIN user u ON m.userid = u.userid 
               ORDER BY m.created DESC 
               LIMIT ?";
        
        return $this->db()->query($sql, [$limit])->fetchAll();
    }
    
    /**
     * Search mods by name or description
     *
     * @param string $query
     * @return array
     */
    public function search($query)
    {
        $sql = "SELECT m.*, u.username 
               FROM mod m 
               JOIN user u ON m.userid = u.userid 
               WHERE m.name LIKE ? OR m.description LIKE ? 
               ORDER BY m.created DESC";
        
        $searchTerm = "%{$query}%";
        
        return $this->db()->query($sql, [$searchTerm, $searchTerm])->fetchAll();
    }
}