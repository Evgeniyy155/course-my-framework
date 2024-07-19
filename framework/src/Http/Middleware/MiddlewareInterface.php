<?php

namespace Web\Framework\Http\Middleware;

use Web\Framework\Http\Request;
use Web\Framework\Http\Response;

interface MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response;
}