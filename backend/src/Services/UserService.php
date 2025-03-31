<?php
namespace App\Services;

use App\Models\User;
use App\Core\Service;

class UserService extends Service
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getUser($id)
    {
        return $this->userModel->find($id);
    }

    /**
     * Get user by username
     *
     * @param string $username
     * @return array|null
     */
    public function getUserByUsername($username)
    {
        return $this->query(
            "SELECT * FROM user WHERE username = ?",
            [$username]
        )->fetch();
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return int
     */
    public function createUser(array $data)
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $data['created'] = date('Y-m-d H:i:s');
        return $this->userModel->create($data);
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUser($id, array $data)
    {
        // Hash password if it's being updated
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->userModel->update($id, $data);
    }

    /**
     * Verify user credentials
     *
     * @param string $username
     * @param string $password
     * @return array|null User data if verified, null otherwise
     */
    public function verifyCredentials($username, $password)
    {
        $user = $this->getUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Don't return password hash
            return $user;
        }

        return null;
    }

    /**
     * Get user's mods
     *
     * @param int $userId
     * @return array
     */
    public function getUserMods($userId)
    {
        return $this->query(
            "SELECT m.*, a.created 
            FROM mod m 
            JOIN asset a ON m.assetid = a.assetid 
            WHERE m.userid = ? 
            ORDER BY a.created DESC",
            [$userId]
        )->fetchAll();
    }
}