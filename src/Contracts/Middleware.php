<?php

namespace Awesome\Connector\Contracts;

use Closure;

interface Middleware
{
    public function __invoke(): ?Closure;
}