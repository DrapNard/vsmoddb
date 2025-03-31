<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'user';
    
    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'userid';
    
    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'displayname',
        'bio',
        'website',
        'location',
        'avatar',
        'rolecode'
    ];
    
    /**
     * Find a user by username
     *
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username)
    {
        return $this->findOneBy('username', $username);
    }
    
    /**
     * Find a user by email
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email)
    {
        return $this->findOneBy('email', $email);
    }
    
    /**
     * Authenticate a user
     *
     * @param string $username
     * @param string $password
     * @return array|null
     */
    public function authenticate($username, $password)
    {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            return null;
        }
        
        if (!password_verify($password, $user['password'])) {
            return null;
        }
        
        return $user;
    }
    
    /**
     * Get user's mods
     *
     * @param int $userId
     * @return array
     */
    public function getMods($userId)
    {
        $sql = "SELECT * FROM mod WHERE userid = ?";
        return $this->db()->query($sql, [$userId])->fetchAll();
    }
    
    /**
     * Get user's notifications
     *
     * @param int $userId
     * @param bool $unreadOnly
     * @return array
     */
    public function getNotifications($userId, $unreadOnly = false)
    {
        $sql = "SELECT * FROM notification WHERE userid = ?";
        
        if ($unreadOnly) {
            $sql .= " AND isread = 0";
        }
        
        $sql .= " ORDER BY created DESC";
        
        return $this->db()->query($sql, [$userId])->fetchAll();
    }
}