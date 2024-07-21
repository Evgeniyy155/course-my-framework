<?php

namespace Web\Framework\Authentication;

interface AuthUserInterface
{
    public function getId(): int;

    public function getEmail(): string;

    public function getPassword(): string;
}