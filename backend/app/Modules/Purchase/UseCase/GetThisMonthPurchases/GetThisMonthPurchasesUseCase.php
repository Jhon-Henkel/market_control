<?php

namespace App\Modules\Purchase\UseCase\GetThisMonthPurchases;

use App\Models\Purchase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class GetThisMonthPurchasesUseCase
{
    public function execute(): array
    {
        $purchases = Purchase::whereMonth('created_at', Date::now()->month)->get();
        if ($purchases->isEmpty()) {
            return [];
        }
        $products = [];
        /** @var Purchase $purchase */
        foreach ($purchases as $purchase) {
            foreach ($purchase->products as $product) {
                if (!isset($products[$product->name])) {
                    $products[$product->name] = [
                        'quantity' => 0,
                        'unit' => $product->unit,
                        'name' => Str::title(Str::lower($product->name)),
                        'total_value' => 0,
                    ];
                }

                $products[$product->name]['quantity'] += $product->quantity;
                $products[$product->name]['total_value'] += $product->total_price;
            }
        }
        ksort($products);
        return [
            'total_amount' => $purchases->sum('total_value'),
            'total_products' => count($products),
            'products' => array_values($products),
        ];
    }
}
