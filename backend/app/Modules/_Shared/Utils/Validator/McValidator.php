<?php

namespace App\Modules\_Shared\Utils\Validator;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin McValidatorReal
 */
class McValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return McValidatorReal::class;
    }
}
