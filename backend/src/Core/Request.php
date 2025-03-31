<?php
namespace App\Core;

class Request
{
    /**
     * Get the request path
     *
     * @return string
     */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        if ($position === false) {
            return $path;
        }
        
        return substr($path, 0, $position);
    }
    
    /**
     * Get the request method
     *
     * @return string
     */
    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Get all request data
     *
     * @return array
     */
    public function getBody()
    {
        $body = [];
        
        if ($this->getMethod() === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        if ($this->getMethod() === 'POST') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        return $body;
    }
    
    /**
     * Get a specific input value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key, $default = null)
    {
        $body = $this->getBody();
        return $body[$key] ?? $default;
    }
    
    /**
     * Check if a specific input exists
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $body = $this->getBody();
        return isset($body[$key]);
    }
    
    /**
     * Get all query parameters
     *
     * @return array
     */
    public function query()
    {
        return $_GET;
    }
    
    /**
     * Get a specific query parameter
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function queryParam($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}