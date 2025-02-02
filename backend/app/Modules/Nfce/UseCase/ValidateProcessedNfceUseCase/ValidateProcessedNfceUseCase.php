<?php

namespace App\Modules\Nfce\UseCase\ValidateProcessedNfceUseCase;

use App\Models\Nfce;
use App\Models\Product;
use App\Models\Purchase;
use App\Modules\Nfce\Exceptions\NfceNotProcessedException;
use App\Modules\Nfce\Exceptions\ProductsQuantityNotMatchWithPurchaseException;

class ValidateProcessedNfceUseCase
{
    public function execute(string $key): null|true
    {
        $nfce = Nfce::where('key', $key)->first();
        if (is_null($nfce)) {
            return null;
        }
        $purchase = Purchase::where('nfce_id', $nfce->id)->first();
        $items = Product::where('purchase_id', $purchase->id)->count();
        ProductsQuantityNotMatchWithPurchaseException::throwIfDiff($purchase->total_items, $items, $nfce->id);
        NfceNotProcessedException::throwIfNotProcessed($nfce, $nfce->id);
        return true;
    }
}
