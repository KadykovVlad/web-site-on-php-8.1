<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Простой роутер: строка-шаблон вида "/category/{slug}" сопоставляется
 * с текущим путём, {slug} попадает в параметры обработчика.
 */
final class Router
{
    /** @var list<array{pattern: string, handler: callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = ['pattern' => $this->toRegex($pattern), 'handler' => $handler];
    }

    /**
     * @return array{0: callable, 1: array<string, string>}|null
     */
    public function match(string $uri): ?array
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/');
        $path = $path === '' ? '/' : $path;

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $path, $matches) === 1) {
                $params = array_filter($matches, static fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY);

                return [$route['handler'], $params];
            }
        }

        return null;
    }

    private function toRegex(string $pattern): string
    {
        $escaped = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);

        return '#^' . $escaped . '$#';
    }
}
