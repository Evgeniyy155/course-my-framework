<?php

namespace Web\Framework\Http;

use Doctrine\DBAL\Connection;
use League\Container\Container;
use Web\Framework\Http\Exceptions\HttpException;
use Web\Framework\Routing\RouterInterface;

class Kernel
{

    private string $appEnv = 'local';

    public function __construct(
        private RouterInterface $router,
        private Container $container,
    )
    {
        $this->appEnv = $this->container->get('APP_ENV');
    }
    public function handle(Request $request): Response
    {
        try {
            [$routeHandler, $vars] = $this->router->dispatch($request, $this->container);
            $responce = call_user_func_array($routeHandler, $vars);
        } catch (\Exception $e){
            $responce = $this->createExceptionResponce($e);
        }

        return $responce;
    }

    private function createExceptionResponce(\Exception $e): Response
    {
        if(in_array($this->appEnv, ['local', 'testing'])){
            throw $e;
        }

        if($e instanceof HttpException){
            return  new Response($e->getMessage(), $e->getStatusCode());
        }

        return new Response('Server error', 500);
    }
}