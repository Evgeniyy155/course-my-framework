<?php

namespace Web\Framework\Http;

use Web\Framework\Http\Response;

class RedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct('', 302, ['location' => $url]);
    }

    public function send()
    {
        header("Location: {$this->getHeader('location')}", true, $this->getStatusCode());
        exit;
    }
}