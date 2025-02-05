<?php

namespace App\Modules\Purchase\UseCase\GetLastPurchase;

use App\Models\Purchase;

class GetLastPurchaseUseCase
{
    public function execute(): array
    {
        $purchase = Purchase::orderBy('id', 'desc')->first();
        Log::info('Última compra encontrada: ' . json_encode($purchase));
        return [
            'purchase' => $purchase,
            'products' => $purchase->products
        ];
    }
}
