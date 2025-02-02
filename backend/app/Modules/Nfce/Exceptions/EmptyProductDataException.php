<?php

namespace App\Modules\Nfce\Exceptions;

use App\Modules\_Shared\Exceptions\HttpExceptions\BadRequestException;

class EmptyProductDataException extends BadRequestException
{
    public function __construct()
    {
        parent::__construct('Empty product data');
    }

    public static function throwIfEmpty(array $array): void
    {
        if (empty($array)) {
            throw new self();
        }
    }
}
