<?php

namespace Web\Framework\Tests;

class SomecodeClass
{
    public function __construct(
        private readonly WebClass $webClass)
    {
    }

    public function getWebClass(): WebClass
    {
        return $this->webClass;
    }
}