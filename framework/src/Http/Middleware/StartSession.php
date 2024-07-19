<?php

namespace Web\Framework\Http\Middleware;

use Web\Framework\Http\Request;
use Web\Framework\Http\Response;
use Web\Framework\Session\SessionInterface;

class StartSession implements MiddlewareInterface
{

    public function __construct(
        private SessionInterface $session
    )
    {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->session->start();
        $request->setSession($this->session);

        return $handler->handle($request);
    }
}