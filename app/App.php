<?php

class App {
    protected $pdo;
    protected $blade;
    protected $basePath;

    public function __construct() {
        $this->pdo = require __DIR__ . '/../config/database.php';
        $this->blade = new Blade(__DIR__ . '/../app/Views', __DIR__ . '/../storage/views');

        /**
         * ===============================
         * FIXED BASE PATH CALCULATION
         * ===============================
         * Handles:
         * /~user/project/public/index.php
         * → basePath = /~user/project
         */
        $scriptName = $_SERVER['SCRIPT_NAME'];          // /~user/project/public/index.php
        $scriptDir  = dirname($scriptName);             // /~user/project/public
        $this->basePath = rtrim(str_replace('/public', '', $scriptDir), '/');

        // Store base URL once
        if (!isset($_SESSION['base_url'])) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $_SESSION['base_url'] = $protocol . '://' . $host . $this->basePath;
        }
    }

    public function run() {
        $routes = require __DIR__ . '/../routes/web.php';

        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // ===============================
        // REMOVE BASE PATH FROM URI
        // ===============================
        if ($this->basePath !== '' && $this->basePath !== '/' && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        foreach ($routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertRouteToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                // Middleware check
                if (isset($route['middleware']) && $route['middleware'] === 'auth') {
                    if (!isset($_SESSION['user_id'])) {
                        $this->redirect('/login');
                        return;
                    }
                }

                list($controller, $action) = explode('@', $route['action']);
                $controllerClass = "App\\Controllers\\" . $controller;
                $controllerFile  = __DIR__ . "/../app/Controllers/{$controller}.php";

                if (!file_exists($controllerFile)) {
                    http_response_code(500);
                    exit("Controller file not found: {$controllerFile}");
                }

                require_once $controllerFile;

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    exit("Controller class not found: {$controllerClass}");
                }

                $controllerInstance = new $controllerClass($this->pdo, $this->blade);
                call_user_func_array([$controllerInstance, $action], $matches);
                return;
            }
        }

        // ===============================
        // 404 HANDLER
        // ===============================
        http_response_code(404);
        echo "<h2>404 - Page Not Found</h2>";
        echo "<p><strong>Requested URL:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
        echo "<p><strong>Processed URI:</strong> " . htmlspecialchars($uri) . "</p>";
        echo "<p><strong>Base Path:</strong> " . htmlspecialchars($this->basePath) . "</p>";
        echo "<h3>Available Routes:</h3><ul>";
        foreach ($routes as $route) {
            echo "<li>{$route['method']} {$route['path']} → {$route['action']}</li>";
        }
        echo "</ul>";
    }

    protected function convertRouteToRegex($route) {
        $route = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        return '#^' . $route . '$#';
    }

    protected function redirect($path) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];

        $location = $protocol . '://' . $host . $this->basePath . $path;
        header("Location: " . $location);
        exit;
    }
}
