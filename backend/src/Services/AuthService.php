<?php
namespace App\Services;

use App\Core\Service;
use App\Core\Session;

class AuthService extends Service
{
    private $userService;
    private $session;
    private $actionToken;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
        $this->session = new Session();
        $this->actionToken = $this->generateActionToken();
    }

    /**
     * Generate a secure action token for CSRF protection
     *
     * @return string
     */
    private function generateActionToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(8)), '+/', '-_'), '=');
    }

    /**
     * Attempt to log in a user
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login($username, $password, $sessionToken = null)
    {
        $user = $sessionToken ? $this->userService->verifySessionToken($sessionToken) : $this->userService->verifyCredentials($username, $password);

        if ($user) {
            $this->actionToken = $this->generateActionToken();
            $this->session->set('user', $user);
            $this->session->set('user_id', $user['userid']);
            $this->session->set('logged_in', true);
            $this->session->set('action_token', $this->actionToken);
            return true;
        }

        return false;
    }

    /**
     * Log out the current user
     *
     * @return void
     */
    public function logout()
    {
        $this->session->destroy();
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->session->get('logged_in', false);
    }

    /**
     * Get current logged in user
     *
     * @return array|null
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userId = $this->session->get('user_id');
        return $this->userService->getUser($userId);
    }

    /**
     * Check if current user has specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        $user = $this->getCurrentUser();
        return $user && isset($user['role']) && $user['role'] === $role;
    }

    /**
     * Check if current user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Get current action token
     *
     * @return string|null
     */
    public function getActionToken()
    {
        return $this->session->get('action_token');
    }

    /**
     * Validate an action token
     *
     * @param string $token
     * @return bool
     */
    public function validateActionToken($token)
    {
        return $token === $this->getActionToken();
    }
}