<?php

namespace Web\Framework\Routing;

class Route
{
    public static function get(string $uri, callable|array $handler, array $middleware = []): array
    {
        return ['GET', $uri, [$handler, $middleware]];
    }
    public static function post(string $uri, callable|array $handler, array $middleware = []): array
    {
        return ['POST', $uri, [$handler, $middleware]];
    }
}