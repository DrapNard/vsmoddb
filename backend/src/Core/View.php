<?php
namespace App\Core;

class View
{
    /**
     * The data to be passed to the view
     *
     * @var array
     */
    protected $data = [];
    
    /**
     * The layout to be used
     *
     * @var string|null
     */
    protected $layout = 'main';
    
    /**
     * Assign a variable to the view
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function assign($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * Assign multiple variables to the view
     *
     * @param array $data
     * @return self
     */
    public function assignMultiple(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
    
    /**
     * Set the layout
     *
     * @param string|null $layout
     * @return self
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }
    
    /**
     * Render a view
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public function render($view, array $data = [])
    {
        // Merge the data
        $data = array_merge($this->data, $data);
        
        // Extract data to make variables available in the view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = BASE_PATH . "/views/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }
        
        include $viewPath;
        
        // Get the content of the buffer
        $content = ob_get_clean();
        
        // If no layout is specified, return the content
        if ($this->layout === null) {
            return $content;
        }
        
        // Otherwise, render the layout with the content
        $layoutPath = BASE_PATH . "/views/layouts/{$this->layout}.php";
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout {$this->layout} not found");
        }
        
        // Start output buffering again
        ob_start();
        
        // Include the layout file
        include $layoutPath;
        
        // Return the final content
        return ob_get_clean();
    }
    
    /**
     * Display a view
     *
     * @param string $view
     * @param array $data
     * @return void
     */
    public function display($view, array $data = [])
    {
        echo $this->render($view, $data);
    }
    
    /**
     * Render a partial view
     *
     * @param string $partial
     * @param array $data
     * @return string
     */
    public function renderPartial($partial, array $data = [])
    {
        // Set layout to null temporarily
        $originalLayout = $this->layout;
        $this->layout = null;
        
        // Render the partial
        $content = $this->render("partials/{$partial}", $data);
        
        // Restore the original layout
        $this->layout = $originalLayout;
        
        return $content;
    }
}