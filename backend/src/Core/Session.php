<?php
namespace App\Core;

class Session
{
    /**
     * Start the session
     *
     * @return void
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Set a session variable
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if a session variable exists
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove a session variable
     *
     * @param string $key
     * @return void
     */
    public static function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Clear all session variables
     *
     * @return void
     */
    public static function clear()
    {
        session_unset();
    }
    
    /**
     * Destroy the session
     *
     * @return void
     */
    public static function destroy()
    {
        session_destroy();
    }
    
    /**
     * Set a flash message
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public static function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Get the flash message and remove it
     *
     * @return array|null
     */
    public static function getFlash()
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        
        return null;
    }
    
    /**
     * Check if a flash message exists
     *
     * @return bool
     */
    public static function hasFlash()
    {
        return isset($_SESSION['flash']);
    }
    
    /**
     * Regenerate the session ID
     *
     * @param bool $deleteOldSession
     * @return bool
     */
    public static function regenerate($deleteOldSession = true)
    {
        return session_regenerate_id($deleteOldSession);
    }
}