<?php

namespace Web\Framework\Http\Events;

use Web\Framework\Event\Event;
use Web\Framework\Http\Request;
use Web\Framework\Http\Response;

class ResponseEvent extends Event
{
    public function __construct(
        private Request $request,
        private Response $response
    )
    {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}