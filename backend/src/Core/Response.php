<?php
namespace App\Core;

class Response
{
    /**
     * Set the response status code
     *
     * @param int $code
     * @return void
     */
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }
    
    /**
     * Set a response header
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setHeader(string $key, string $value)
    {
        header("$key: $value");
    }
    
    /**
     * Send a JSON response
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    public function json($data, int $statusCode = 200)
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->setStatusCode($statusCode);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redirect to a specific URL
     *
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    public function redirect(string $url, int $statusCode = 302)
    {
        $this->setHeader('Location', $url);
        $this->setStatusCode($statusCode);
        exit;
    }
    
    /**
     * Send a success response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    public function success($data = null, string $message = 'Success', int $statusCode = 200)
    {
        $this->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    /**
     * Send an error response
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return void
     */
    public function error(string $message = 'Error', int $statusCode = 400, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        $this->json($response, $statusCode);
    }
}