<?php
namespace App\Core;

class Controller
{
    /**
     * Render a view
     *
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function render($view, $data = [])
    {
        $viewPath = BASE_PATH . "/views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }
        
        // Extract data to make variables available in the view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        include $viewPath;
        
        // Get the content of the buffer and clean it
        $content = ob_get_clean();
        
        // Output the content
        echo $content;
    }
    
    /**
     * Get the request instance
     *
     * @return Request
     */
    protected function request()
    {
        return App::get('request');
    }
    
    /**
     * Get the response instance
     *
     * @return Response
     */
    protected function response()
    {
        return App::get('response');
    }
    
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
     * Redirect to a specific URL
     *
     * @param string $url
     * @return void
     */
    protected function redirect($url)
    {
        $this->response()->redirect($url);
    }
    
    /**
     * Return a JSON response
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    protected function json($data, $statusCode = 200)
    {
        $this->response()->json($data, $statusCode);
    }
}