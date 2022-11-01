<?php

namespace Awesome\Connector\Exceptions;

use Awesome\Rest\Exceptions\AbstractException;

class ConnectorException extends AbstractException
{
    public const SYMBOLIC_CODE = 'connector_exception';
}