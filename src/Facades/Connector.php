<?php

namespace Awesome\Connector\Facades;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;
use GuzzleHttp\Promise\PromiseInterface;
use Awesome\Connector\Contracts\Request;

/**
 * @method static array|Response send(Request ...$requests)
 * @method static PromiseInterface promise(Request ...$requests)
**/
class Connector extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'connector';
    }
}
