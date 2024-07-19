<?php

namespace Web\Framework\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use League\Container\Container;
use Web\Framework\Controller\AbstractController;
use Web\Framework\Http\Exceptions\MethodNotAllowedException;
use Web\Framework\Http\Exceptions\RouteNotFoundException;
use Web\Framework\Http\Request;
use function FastRoute\simpleDispatcher;

class Router implements RouterInterface
{
    private array $routes;
    public function dispatch(Request $request, Container $container): array
    {
        [$handler, $vars] = $this->extractRouteInfo($request);
        if(is_array($handler)){
            [$controllerId, $method] = $handler;
            $controller = $container->get($controllerId);
            if(is_subclass_of($controller, AbstractController::class)){
                $controller->setRequest($request);
            }
            $handler = [$controller, $method];
        }
        return [$handler, $vars];
    }

    public function registerRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    private function extractRouteInfo(Request $request)
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $collector) {
            foreach ($this->routes as $route){
                $collector->addRoute(...$route);
            }
        });
        $routeInfo = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getPath()
        );

        switch ($routeInfo[0]){
            case Dispatcher::FOUND:
                return [$routeInfo[1], $routeInfo[2]];
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = implode(',', $routeInfo[1]);
                $exception = new MethodNotAllowedException("Support HTTP methods $allowedMethods");
                $exception->setStatusCode(405);
                throw $exception;
            default:
                $exception = new RouteNotFoundException('Route not found');
                $exception->setStatusCode(404);
                throw $exception;
        }
    }


}