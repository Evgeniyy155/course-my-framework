<?php

namespace Web\Framework\Http;

class Response
{
    public function __construct(
        private string $content = '',
        private int $statusCode = 200,
        private array $headers = []
    ) {
        http_response_code($this->statusCode);
    }

    public function send()
    {
        echo $this->content;
    }

    public function setContent(string $content): Response
    {
        $this->content = $content;
        return $this;
    }
}