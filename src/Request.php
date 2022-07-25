<?php

namespace Awesome\Connector;

use Closure;
use Illuminate\Http\Response;
use Psr\Http\Message\RequestInterface;
use Awesome\Connector\Contracts\Method;
use Awesome\Connector\Traits\DataConverter;
use GuzzleHttp\Psr7\Request as HttpRequest;
use Awesome\Connector\Contracts\Request as RequestContract;

class Request implements RequestContract
{
    use DataConverter;

    protected string $method = Method::GET;
    protected string $url = '';

    protected array $headers = [];
    protected string $body;
    protected array $options = [
        'timeout' => 2
    ];

    protected Closure $successCallback;
    protected Closure $errorCallback;

    public function method(string $method = null): string|RequestContract
    {
        return $this->set('method', $method);
    }

    public function url(string $url = null): string|RequestContract
    {
        return $this->set('url', $url);
    }

    public function query(array $query = null): array|RequestContract
    {
        return $this->option('query', $query);
    }

    public function body(array|string $body = null): array|string|RequestContract
    {
        if (is_array($body)) {
            $this->headers(['Content-Type' => 'application/json']);
            $body = $this->toJson($body);
        }

        return $this->set('body', $body);
    }

    public function formData(array $data = null): array|RequestContract
    {
        return $this->set('body', is_null($data) ? $data : $this->toMultipartStream($data));
    }

    public function headers(array $headers = null): array|RequestContract
    {
        return $this->set('headers', $headers);
    }

    public function options(array $options = null): array|RequestContract
    {
        return $this->set('options', $options);
    }

    public function option(string $key, $value = null)
    {
        if (is_null($value)) {
            return $this->options[$key] ?? [];
        }

        $this->options[$key] = $value;

        return $this;
    }

    public function success(Closure $callback = null): Closure|RequestContract
    {
        return $this->set('successCallback', $callback);
    }

    public function error(Closure $callback = null): Closure|RequestContract
    {
        return $this->set('errorCallback', $callback);
    }

    public function makeHttpRequest(): RequestInterface
    {
        return new HttpRequest($this->method(), $this->url(), $this->headers(), $this->body());
    }

    public function send(): Response
    {
        return Connector::send($this);
    }

    protected function set(string $key, string|array|Closure $value = null): string|array|Closure|RequestContract
    {
        if (is_null($value)) {
            return $this->$key;
        }

        $this->$key = is_array($value) ? array_merge($this->$key, $value) : $value;

        return $this;
    }
}
