<?php

namespace App\Modules\Nfce\UseCase\RevertNfce;

use App\Models\Nfce;
use App\Models\Product;
use App\Models\Purchase;

class RevertNfceUseCase
{
    public function execute(int $nfceId): void
    {
        $nfce = Nfce::find($nfceId);
        $purchase = Purchase::where('nfce_id', $nfce->id)->first();
        $products = Product::where('purchase_id', $purchase->id)->get();

        foreach ($products as $product) {
            $product->delete();
        }

        $purchase->delete();
        $nfce->delete();
    }
}
