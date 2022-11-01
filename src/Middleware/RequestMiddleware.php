<?php

namespace Awesome\Connector\Middleware;

use Awesome\Connector\Contracts\Middleware as MiddlewareContract;
use Closure;
use GuzzleHttp\Middleware;

abstract class RequestMiddleware implements MiddlewareContract
{
    public function __invoke(): ?Closure
    {
        return Middleware::mapRequest($this->handle());
    }
    
    abstract public function handle(): Closure;
}