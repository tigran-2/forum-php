<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Simple router with support for GET/POST, parameters, and middleware.
 */
class Router
{
    /** @var array<string, array<string, array{handler: callable, middleware: array}>> */
    private array $routes = [];

    /** @var array<string, callable> */
    private array $middleware = [];

    private string $basePath = '';

    public function setBasePath(string $basePath): void
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Register middleware.
     */
    public function addMiddleware(string $name, callable $handler): void
    {
        $this->middleware[$name] = $handler;
    }

    /**
     * Add a GET route.
     */
    public function get(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Add a POST route.
     */
    public function post(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Add a route for any method.
     */
    public function any(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, callable $handler, array $middleware): void
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Remove base path
        if ($this->basePath && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath)) ?: '/';
        }

        // Normalize path
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        // Find matching route
        $route = $this->findRoute($method, $path);

        if (!$route) {
            $this->notFound();
            return;
        }

        // Run middleware
        foreach ($route['middleware'] as $middlewareName) {
            if (isset($this->middleware[$middlewareName])) {
                $result = ($this->middleware[$middlewareName])();
                if ($result === false) {
                    return;
                }
            }
        }

        // Call handler with parameters
        call_user_func($route['handler'], ...$route['params']);
    }

    private function findRoute(string $method, string $path): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $routePath => $route) {
            $params = $this->matchPath($routePath, $path);
            if ($params !== null) {
                return [
                    'handler' => $route['handler'],
                    'middleware' => $route['middleware'],
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    /**
     * Match a route path against the current path.
     * Returns params array on match, null on no match.
     */
    private function matchPath(string $routePath, string $currentPath): ?array
    {
        // Exact match
        if ($routePath === $currentPath) {
            return [];
        }

        // Pattern match with parameters like {id}
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $currentPath, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return array_values($params);
        }

        return null;
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '404 Not Found';
    }
}
