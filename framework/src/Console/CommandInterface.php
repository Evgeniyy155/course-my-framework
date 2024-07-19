<?php

namespace Web\Framework\Console;

interface CommandInterface
{
    public function execute(array $parameters = []): int;
}