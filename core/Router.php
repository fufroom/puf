<?php

namespace Puf\Core;

class Router
{
    private $routes = [];

    public function addRoute($method, $path, $callback)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => rtrim($path, '/'), // Ensure consistent path format
            'callback' => $callback,
        ];
    }

    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = preg_replace('/\/+/', '/', rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')); // Remove duplicate slashes

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', trim($route['path'], '/'));

            if ($route['method'] === $requestMethod && preg_match("#^$pattern$#", trim($requestPath, '/'), $matches)) {
                array_shift($matches);
                call_user_func_array($route['callback'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo "Not Found: " . htmlspecialchars($requestPath);
    }
}
