<?php

namespace Awesome\Connector;

use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Awesome\Connector\Contracts\Status;
use Awesome\Connector\Contracts\Request;
use GuzzleHttp\Promise\PromiseInterface;
use Awesome\Connector\Contracts\Connector as ConnectorContract;

class Connector implements ConnectorContract
{
    private ?string $baseUrl;
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

    private function prepareRequest(Request $request): PromiseInterface
    {
        return $this->httpClient
            ->sendAsync($request->makeHttpRequest(), $request->options())
            ->then(
                $this->onFulfilled($request->success()),
                $this->onRejected($request->error())
            );
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

    public function convertResponse(ResponseInterface $psrResponse = null): Response
    {
        return new Response(
            optional($psrResponse)->getBody(),
            optional($psrResponse)->getStatusCode() ?? Status::SERVER_ERROR,
            optional($psrResponse)->getHeaders() ?? []
        );
    }

    private function initHttpClient(): void
    {
        $this->httpClient = new Client(['base_uri' => $this->baseUrl]);
    }
}
