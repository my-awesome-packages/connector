<?php

namespace Awesome\Connector\Contracts;

use Illuminate\Http\Response;

interface Connector
{
    public function send(): Response;
}
