<?php

namespace App\Modules\_Shared\Exceptions\HttpExceptions;

use App\Modules\_Shared\Enum\Response\HttpStatusCodeEnum;
use RuntimeException;

class BadRequestException extends RuntimeException
{
    protected $code = HttpStatusCodeEnum::HttpBadRequest->value;
}
