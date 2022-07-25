<?php

namespace Awesome\Connector\Contracts;

interface Status
{
    // Success
    const OK = 200;

    // Redirection
    const MOVED_PERMANENTLY = 301;
    const MOVED_TEMPORARILY = 302;

    // Client Error
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const NOT_ALLOWED = 405;
    const MANY_REQUESTS = 429;

    // Server Error
    const SERVER_ERROR = 500;
    const BAD_GATEWAY = 502;
    const SERVER_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
}
