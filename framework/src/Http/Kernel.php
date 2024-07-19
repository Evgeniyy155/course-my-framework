<?php

namespace Web\Framework\Http;

use Doctrine\DBAL\Connection;
use League\Container\Container;
use Web\Framework\Http\Exceptions\HttpException;
use Web\Framework\Http\Middleware\RequestHandlerInterface;
use Web\Framework\Routing\RouterInterface;

class Kernel
{

    private string $appEnv = 'local';

    public function __construct(
        private RouterInterface $router,
        private Container $container,
        private RequestHandlerInterface $requestHandler,
    )
    {
        $this->appEnv = $this->container->get('APP_ENV');
    }
    public function handle(Request $request): Response
    {
        try {
            $responce = $this->requestHandler->handle($request);

        } catch (\Exception $e){
            $responce = $this->createExceptionResponce($e);
        }

        return $responce;
    }
    public function terminate(Request $request, Response $response): void
    {
        $request->getSession()?->clearFlash();
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