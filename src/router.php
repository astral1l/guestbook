<?php

$routes = [];

function route(string $method, string $path, callable $handler): void
{
    global $routes;

    $routes[] = [
        'method' => $method,
        'path' => $path,
        'handler' => $handler,
    ];
}

function dispatch(string $method, string $uri): void
{
    global $routes;

    foreach ($routes as $route) {
        if ($route['method'] !== $method) {
            continue;
        }

        $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>\d+)', $route['path']) . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $route['handler']($params);

            return;
        }
    }

    render('error', ['title' => 'Page not found'], 404);
}
