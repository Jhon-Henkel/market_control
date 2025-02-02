<?php

namespace App\Modules\_Shared\Enum\Response;

enum HttpResponseLevelEnum: string
{
    case Validation = 'validation';
    case Error = 'error';
}
