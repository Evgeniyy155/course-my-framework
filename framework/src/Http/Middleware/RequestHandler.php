<?php

namespace Web\Framework\Http\Middleware;

use Psr\Container\ContainerInterface;
use Web\Framework\Http\Request;
use Web\Framework\Http\Response;

class RequestHandler implements RequestHandlerInterface
{
    private array $middleware = [
        StartSession::class,
        Authenticate::class,
        RouterDispatch::class,
    ];

    public function __construct(
        private ContainerInterface $container
    )
    {
    }

    public function handle(Request $request): Response
    {
        // Якщо немає middleware-класів для виконання, вернуть відповідь за замовчуванням
        // Відопвідь повинна бути повернута до того як список стане пустим
        if(empty($this->middleware)){
            return new Response("Server error", 500);
        }
        // Отримати наступний middleware-клас для виконання

        $middlewareClass = array_shift($this->middleware);
        // Створити новий екземпляр визову процесу middleware на ньому

        $middleware = $this->container->get($middlewareClass);
        $responce = $middleware->process($request, $this);

        return $responce;
    }
}