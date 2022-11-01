<?php

namespace Awesome\Connector\Facades;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;
use GuzzleHttp\Promise\PromiseInterface;
use Awesome\Connector\Contracts\{Connector as ConnectorContract, Request};

/**
 * @method static array|Response send(Request ...$requests)
 * @method static PromiseInterface promise(Request ...$requests)
 * @method static ConnectorContract withMiddleware(string ...$middleware)
 * @method static ConnectorContract withoutMiddleware(string ...$middleware)
**/
class Connector extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'connector';
    }
}
