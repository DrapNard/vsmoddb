<?php
namespace App\Core;

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];
    
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function put($path, $callback)
    {
        $this->routes['PUT'][$path] = $callback;
    }
    
    public function delete($path, $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
    }
    
    public function resolve($path, $method)
    {
        // Extract route parameters
        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = preg_replace('/{([a-zA-Z0-9_]+)}/', '(?P<$1>[^/]+)', $route);
            $pattern = "#^$pattern$#";
            
            if (preg_match($pattern, $path, $matches)) {
                // Filter out numeric keys
                $params = array_filter($matches, function($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);
                
                if (is_callable($callback)) {
                    return call_user_func_array($callback, $params);
                }
                
                if (is_array($callback)) {
                    [$controller, $method] = $callback;
                    $controller = new $controller();
                    return call_user_func_array([$controller, $method], $params);
                }
            }
        }
        
        // Route not found
        App::get('response')->setStatusCode(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Route not found'
        ]);
    }
}