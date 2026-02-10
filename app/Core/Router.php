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

        // CSRF Check for POST requests
        if ($method === 'post') {
            // Check headers or body
            $token = $_POST['csrf_token'] ?? null;
            
            // Should also check JSON body if content-type is json
            if (!$token) {
                $input = json_decode(file_get_contents('php://input'), true);
                $token = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            }

            if (!Auth::verifyCsrfToken($token)) {
                $this->response->setStatusCode(403);
                
                // Return JSON if expectation is JSON
                if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                     echo json_encode(['status' => 'error', 'message' => 'CSRF Token Mismatch']);
                     return;
                }

                return "403 - Forbidden: Invalid CSRF Token";
            }
        }
        
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
            // Check if Container is available via global helper or static?
            // To be clean, Router should have the Container passed to it.
            // Or we instantiate just the controller via Container here internally.
            
            $container = new \App\Core\Container(); // Ideally injected, but for refactor this works
            // Bind core classes if needed, or rely on autowiring
            
            try {
                // Try to resolve from Container first
                $controller = \App\Core\Container::get($callback[0]);
                
                // If Container returns null (not found), instantiate directly
                if (!$controller) {
                    $controller = new $callback[0]();
                }
            } catch (\Throwable $e) {
                // If instantiation fails, we can't recover.
                // But for resilience, we might want to log it.
                error_log("Router failed to instantiate controller: " . $e->getMessage());
                throw $e;
            }

            if (method_exists($controller, 'setRequest')) {
                $controller->setRequest($this->request);
            }
            $method = $callback[1];
            return call_user_func([$controller, $method]);
        }

        return call_user_func($callback);
    }
}
