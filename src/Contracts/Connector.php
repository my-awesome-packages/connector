<?php

namespace Awesome\Connector\Contracts;

use Awesome\Connector\Contracts\Connector as ConnectorContract;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;

interface Connector
{
    public function send(): Response;

    public function promise(Request ...$requests): PromiseInterface;

    public function convertResponse(ResponseInterface $psrResponse = null): Response;

    public function withMiddleware(string ...$middleware): ConnectorContract;

    public function withoutMiddleware(string ...$middleware): ConnectorContract;
}
