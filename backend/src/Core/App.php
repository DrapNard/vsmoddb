<?php
namespace App\Core;

class App
{
    private static $container = [];
    private $router;
    
    public function __construct()
    {   
        // Create router instance
        $this->router = new Router();
        
        // Register core services
        self::$container['router'] = $this->router;
        self::$container['request'] = new Request();
        self::$container['response'] = new Response();
        self::$container['view'] = new View();
        self::$container['db'] = new Database(
            DB_HOST, 
            DB_NAME, 
            DB_USER, 
            DB_PASS
        );
    }
    
    public function run()
    {
        try {
            $this->router->resolve(
                self::$container['request']->getPath(),
                self::$container['request']->getMethod()
            );
        } catch (\Exception $e) {
            if (DEBUG) {
                // Show detailed error in development
                self::$container['response']->setStatusCode(500);
                echo '<h1>Error</h1>';
                echo '<p>' . $e->getMessage() . '</p>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
            } else {
                // Show generic error in production
                self::$container['response']->setStatusCode(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'An internal server error occurred'
                ]);
            }
        }
    }
    
    public static function get($key)
    {
        return self::$container[$key] ?? null;
    }
    
    public static function set($key, $value)
    {
        self::$container[$key] = $value;
    }
}