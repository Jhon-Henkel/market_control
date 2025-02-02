<?php

namespace App\Modules\Purchase\Factory;

use App\Modules\Nfce\DTO\NfceOutputDTO;
use App\Modules\Purchase\DTO\PurchaseInputDTO;

class PurchaseFactory
{
    public function makeInputDtoByArray(array $purchase, NfceOutputDTO $nfce): PurchaseInputDTO
    {
        return new PurchaseInputDTO(
            totalItems: $purchase['total_items'],
            nfceId: $nfce->getId(),
            subtotal: $purchase['subtotal'],
            discount: $purchase['discount'],
            amount: $purchase['amount_to_pay'],
            purchaseDate: $nfce->getEmission()
        );
    }
}
