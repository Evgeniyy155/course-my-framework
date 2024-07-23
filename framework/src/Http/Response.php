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
        ob_start();
        foreach ($this->headers as $key => $value){
            header("{$key}: {$value}");
        }
        echo $this->content;

        ob_end_flush();
    }

    public function setContent(string $content): Response
    {
        $this->content = $content;
        return $this;
    }

    public function getHeader(string $key)
    {
        return $this->headers[$key];
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader(string $key, mixed $value): void
    {
        $this->headers[$key] = $value;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}