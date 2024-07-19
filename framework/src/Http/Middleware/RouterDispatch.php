<?php

namespace Web\Framework\Http\Middleware;

use Psr\Container\ContainerInterface;
use Web\Framework\Http\Middleware\MiddlewareInterface;
use Web\Framework\Http\Request;
use Web\Framework\Http\Response;
use Web\Framework\Routing\RouterInterface;

class RouterDispatch implements MiddlewareInterface
{
    public function __construct(
        private RouterInterface $router,
        private ContainerInterface $container
    )
    {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        [$routeHandler, $vars] = $this->router->dispatch($request, $this->container);
        $responce = call_user_func_array($routeHandler, $vars);
        return $responce;
    }
}