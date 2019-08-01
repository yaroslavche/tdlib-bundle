<?php

namespace Yaroslavche\TDLibBundle\Exception;

use Exception;

class InvalidResponseException extends Exception
{
    public const INVALID_JSON = 0;
    public const UNRECOGNIZED_TYPE = 1;
    public const UNRECOGNIZED_PROPERTY = 2;
}
