<?php

namespace Awesome\Connector\Contracts;

use Closure;
use GuzzleHttp\Psr7\MultipartStream;
use Illuminate\Http\Response;
use Psr\Http\Message\RequestInterface;

interface Request
{
    public function method(string $method = null): string|Request;

    public function url(string $url = null): string|Request;

    public function query(array $query = null): array|Request;

    public function formData(array $data): Request;

    public function body(string|array $body = null): string|array|MultipartStream|Request;

    public function headers(array $headers = null): array|Request;

    public function options(array $options = null): array|Request;

    public function option(string $key, $value = null);

    public function success(Closure $callback = null): null|Closure|Request;

    public function error(Closure $callback = null): null|Closure|Request;

    public function makeHttpRequest(): RequestInterface;

    public function send(): Response;
}
