<?php

namespace App\Core;

class Router
{
    private Request $request;
    private Response $response;
    private array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, callable|array $callback): void
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, callable|array $callback): void
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        
        $callback = $this->routes[$method][$path] ?? false;

        // If not found, try pattern matching for dynamic routes
        if ($callback === false) {
            foreach ($this->routes[$method] ?? [] as $route => $handler) {
                // Convert route pattern to regex
                $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route);
                $pattern = '#^' . $pattern . '$#';
                
                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches); // Remove full match
                    $callback = $handler;
                    
                    // Call with params
                    if (is_array($callback)) {
                        $controller = new $callback[0]();
                        $method = $callback[1];
                        return call_user_func_array([$controller, $method], $matches);
                    }
                    return call_user_func_array($callback, $matches);
                }
            }
        }

        if ($callback === false) {
            $this->response->setStatusCode(404);
            return "404 - Not Found";
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $method = $callback[1];
            return call_user_func([$controller, $method]);
        }

        return call_user_func($callback);
    }
}
