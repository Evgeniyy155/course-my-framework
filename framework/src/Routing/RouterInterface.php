<?php

namespace Web\Framework\Routing;

use League\Container\Container;
use Web\Framework\Http\Request;

interface RouterInterface
{
    public function dispatch(Request $request, Container $container): array;

    public function registerRoutes(array $routes): void;
}