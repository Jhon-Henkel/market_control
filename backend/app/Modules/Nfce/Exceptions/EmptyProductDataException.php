<?php

namespace App\Modules\Nfce\Exceptions;

use App\Modules\_Shared\Exceptions\HttpExceptions\BadRequestException;
use Illuminate\Support\Facades\Log;

class EmptyProductDataException extends BadRequestException
{
    public function __construct()
    {
        parent::__construct('Empty product data');
    }

    public static function throwIfEmpty(array $array): void
    {
        Log::error('Empty product data. Data: ' . json_encode($array));
        if (empty($array)) {
            throw new self();
        }
    }
}
