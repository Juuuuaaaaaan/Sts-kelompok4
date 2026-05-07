<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $uri, string $controller, string $function)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'function' => $function,
        ];
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pattern = str_replace(
                '{id}',
                '([0-9]+)',
                $route['uri']
            );

            $pattern = '#^' . $pattern . '$#';

            if ($method === $route['method'] && preg_match($pattern, $uri, $matches)) {
                
                require_once __DIR__ . '/../controllers/' . $route['controller'] . '.php';
                
                array_shift($matches);
             
            
                $controllerClass = 'App\\Controllers\\' . $route['controller'];
                $controller = new $controllerClass();
                $function = $route['function'];
                
               
                call_user_func_array([$controller, $function], $matches);

                return;
            }
        }
         
        http_response_code(404);
        echo '<div style="text-align:center; padding: 50px; font-family: sans-serif;">';
        echo '<h1 style="font-size: 3rem; color: #333;">404 - Page Not Found</h1>';
        echo '<p style="color: #666;">Halaman yang kamu cari tidak ditemukan di server.</p>';
        echo '<a href="/" style="color: #3b82f6; text-decoration: underline;">Kembali ke Home</a>';
        echo '</div>';
    }
}