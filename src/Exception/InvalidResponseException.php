<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\Exception;

use Exception;

class InvalidResponseException extends Exception
{
    public const INVALID_JSON = 0;
    public const UNRECOGNIZED_TYPE = 1;
    public const UNRECOGNIZED_PROPERTY = 2;
    public const INVALID_VALUE = 3;
}
