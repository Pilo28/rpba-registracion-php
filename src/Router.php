<?php

declare(strict_types=1);

namespace App;

final class Router
{
    /** @var array<int, array{method: string, pattern: string, params: string[], handler: callable}> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    private function map(string $method, string $path, callable $handler): void
    {
        [$pattern, $params] = $this->compile($path);
        $this->routes[] = ['method' => $method, 'pattern' => $pattern, 'params' => $params, 'handler' => $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                $args = [];
                foreach ($route['params'] as $name) {
                    $args[$name] = $matches[$name] ?? null;
                }
                ($route['handler'])($args);
                return;
            }
        }

        http_response_code(404);
        (new Controllers\NotFoundController())->show();
    }

    /**
     * Convierte '/manual/{slug}' en una regex con grupos con nombre.
     * @return array{0: string, 1: string[]}
     */
    private function compile(string $path): array
    {
        $params = [];
        $pattern = preg_replace_callback('#\{(\w+)\}#', function ($m) use (&$params) {
            $params[] = $m[1];
            return '(?P<' . $m[1] . '>[^/]+)';
        }, rtrim($path, '/') ?: '/');

        return ['#^' . $pattern . '$#', $params];
    }
}
