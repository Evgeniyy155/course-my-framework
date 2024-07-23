<?php

namespace App\Entities;

use Web\Framework\Dbal\Entity;

class Post extends Entity
{
    public function __construct(
        private ?int $id,
        private string $title,
        private string $body,
        private \DateTimeImmutable|null $createdAt
    )
    {
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public static function create(
        string $title,
        string $body,
        ?int $id = null,
        \DateTimeImmutable|null $createdAt = null
    ): static {
      return new static($id, $title, $body, $createdAt ?? new \DateTimeImmutable());
    }

}