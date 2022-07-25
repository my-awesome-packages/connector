<?php

namespace Awesome\Connector\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
**/
class Connector extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'connector';
    }
}
