<?php
class Router {
    private $routes = [];

    public function addRoute($path, $handler) {
        $this->routes[$path] = $handler;
    }

    public function dispatch() {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove base path from URI
        $basePath = '/Codes/Accounting-Automation';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // If URI is empty or just '/', set it to root path
        if (empty($uri) || $uri === '/') {
            $uri = '/';
        }
        
        if (array_key_exists($uri, $this->routes)) {
            list($controller, $method) = explode('@', $this->routes[$uri]);
            $controllerFile = "controllers/{$controller}.php";
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerInstance = new $controller();
                $controllerInstance->$method();
            } else {
                $this->handleError(404);
            }
        } else {
            $this->handleError(404);
        }
    }

    private function handleError($code) {
        http_response_code($code);
        require_once "views/errors/{$code}.php";
    }
} 