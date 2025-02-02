<?php

namespace App\Modules\Nfce\Exceptions;

use App\Modules\_Shared\Exceptions\HttpExceptions\BadRequestException;

class ProductsQuantityNotMatchWithPurchaseException extends BadRequestException
{
    public static ?int $nfceId;

    public function __construct()
    {
        parent::__construct('A quantidade de produtos não bate com a quantidade da compra');
    }

    public static function throwIfDiff(int $productsCount, int $purchaseCount, ?int $nfceId = null): void
    {
        if ($productsCount !== $purchaseCount) {
            self::$nfceId = $nfceId;
            throw new self();
        }
    }
}
