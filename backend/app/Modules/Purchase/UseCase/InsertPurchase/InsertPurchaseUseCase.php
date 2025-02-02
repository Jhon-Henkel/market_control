<?php

namespace App\Modules\Purchase\UseCase\InsertPurchase;

use App\Models\Purchase;
use App\Modules\Purchase\DTO\PurchaseInputDTO;
use App\Modules\Purchase\DTO\PurchaseOutputDTO;

class InsertPurchaseUseCase
{
    public function execute(PurchaseInputDTO $input): PurchaseOutputDTO
    {
        $purchase = Purchase::create([
            'total_items' => $input->getTotalItems(),
            'nfce_id' => $input->getNfceId(),
            'subtotal_value' => $input->getSubtotal(),
            'discount_value' => $input->getDiscount(),
            'total_value' => $input->getAmount(),
            'purchase_date' => $input->getPurchaseDate(),
        ]);
        return new PurchaseOutputDTO($input, $purchase);
    }
}
