<?php

namespace App\Modules\Nfce\Exceptions;

use App\Modules\_Shared\Exceptions\HttpExceptions\BadRequestException;

class ProductsQuantityNotMatchWithPurchaseException extends BadRequestException
{
    public function __construct()
    {
        parent::__construct('A quantidade de produtos não bate com a quantidade da compra');
    }

    public static function throwIfDiff(int $productsCount, int $purchaseCount): void
    {
        if ($productsCount !== $purchaseCount) {
            throw new self();
        }
    }
}
