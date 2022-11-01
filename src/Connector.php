<?php

namespace Awesome\Connector;

use Awesome\Connector\Contracts\{Connector as ConnectorContract, Middleware, Request, Status};
use Closure;
use Exception;
use GuzzleHttp\{Client, HandlerStack, Promise};
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;

class Connector implements ConnectorContract
{
    private ?string $baseUrl;
    private array $middleware = [];
    private Client $httpClient;

    public function __construct()
    {
        $this->baseUrl = config('connect.base_url');

        $this->initHttpClient();
    }

    public function send(Request ...$requests): Response
    {
        $result = $this->promise(...$requests)->wait();
        return (count($result) === 1) ? head($result) : $result;
    }

    public function promise(Request ...$requests): PromiseInterface
    {
        $requests = array_map(function ($request) {
            return $this->prepareRequest($request);
        }, $requests);

        return Promise\Utils::all($requests);
    }

    public function convertResponse(ResponseInterface $psrResponse = null): Response
    {
        return new Response(
            optional($psrResponse)->getBody(),
            optional($psrResponse)->getStatusCode() ?? Status::SERVER_ERROR,
            optional($psrResponse)->getHeaders() ?? []
        );
    }

    public function withMiddleware(string ...$middleware): ConnectorContract
    {
        if (!empty($middleware)) {
            return $this->applyMiddleware(array_merge($this->middleware, $middleware));
        }

        return $this;
    }

    public function withoutMiddleware(string ...$middleware): ConnectorContract
    {
        if (!empty($middleware)) {
            return $this->applyMiddleware(array_diff($this->middleware, $middleware));
        }

        return $this;
    }

    private function prepareRequest(Request $request): PromiseInterface
    {
        return $this->httpClient
            ->sendAsync($request->makeHttpRequest(), $request->options())
            ->then(
                $this->onFulfilled($request->success()),
                $this->onRejected($request->error())
            );
    }

    private function getHandlerStack(): HandlerStack
    {
        $stack = HandlerStack::create(new CurlMultiHandler());

        foreach ($this->middleware as $name => $class) {
            $middleware = new $class();

            if ($middleware instanceof Middleware) {
                $stack->push($middleware(), $name);
            }
        }

        return $stack;
    }

    private function onFulfilled(Closure $callback = null)
    {
        return function (ResponseInterface $response) use ($callback) {
            $response = $this->convertResponse($response);

            if (is_callable($callback)) {
                return $callback($response) ?? $response;
            }

            return $response;
        };
    }

    private function onRejected(Closure $callback = null)
    {
        return function (Exception $exception) use ($callback) {
            $response = $this->convertResponse()->withException($exception);

            if (is_callable($callback)) {
                return $callback($exception) ?? $response;
            }

            return $response;
        };
    }

    private function initHttpClient(): ConnectorContract
    {
        $this->httpClient = new Client(['base_uri' => $this->baseUrl, 'handler' => $this->getHandlerStack()]);
        
        return $this;
    }

    private function applyMiddleware(array $middleware): ConnectorContract
    {
        return tap(clone $this, function (ConnectorContract $new) use ($middleware) {
            $new->middleware = $middleware;
            $new->initHttpClient();
        });
    }
}
