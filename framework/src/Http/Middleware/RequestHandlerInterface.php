<?php

namespace Web\Framework\Http\Middleware;

use Web\Framework\Http\Request;
use Web\Framework\Http\Response;

interface RequestHandlerInterface
{
    public function handle(Request $request): Response;
}