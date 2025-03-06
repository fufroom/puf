<?php

namespace Puf\Core;

class Router
{
    private $routes = [];
    private $auth;
    private $protectedRoutes = [];
    private $loginUrl;
    private $logoutUrl;

    public function __construct($config)
    {
        if (!isset($config['auth_routes'])) {
            throw new \Exception("Missing 'auth_routes' configuration.");
        }

        $this->auth = new SimpleAuth($config);
        $this->loginUrl = $config['auth_routes']['login'];
        $this->logoutUrl = $config['auth_routes']['logout'];
        $this->protectedRoutes = $config['auth_routes']['protected'] ?? [];
    }

    public function addRoute($method, $path, $callback)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => rtrim($path, '/'),
            'callback' => $callback,
        ];
    }

    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = preg_replace('/\/+/', '/', rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

        if (in_array($requestPath, $this->protectedRoutes) && !$this->auth->isLoggedIn()) {
            header("Location: " . $this->loginUrl);
            exit;
        }

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', trim($route['path'], '/'));

            if ($route['method'] === $requestMethod && preg_match("#^$pattern$#", trim($requestPath, '/'), $matches)) {
                array_shift($matches);
                call_user_func_array($route['callback'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["error" => "Route not found: " . htmlspecialchars($requestPath)]);
    }
}
