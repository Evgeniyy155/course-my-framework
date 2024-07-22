<?php

namespace Web\Framework\Http\Middleware;

use Web\Framework\Authentication\SessionAuthInterface;
use Web\Framework\Http\Middleware\MiddlewareInterface;
use Web\Framework\Http\RedirectResponce;
use Web\Framework\Http\Request;
use Web\Framework\Http\Response;
use Web\Framework\Session\SessionInterface;

class Guest implements MiddlewareInterface
{
    public function __construct(
        private SessionAuthInterface $auth,
        private SessionInterface $session
    )
    {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->session->start();
        if($this->auth->check()){
            return new RedirectResponce('/dashboard');
        }
        return  $handler->handle($request);
    }
}