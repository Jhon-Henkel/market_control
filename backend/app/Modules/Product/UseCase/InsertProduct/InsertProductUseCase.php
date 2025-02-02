<?php

namespace App\Modules\Product\UseCase\InsertProduct;

use App\Models\Product;
use App\Modules\Product\DTO\ProductInputDTO;
use App\Modules\Purchase\DTO\PurchaseOutputDTO;

class InsertProductUseCase
{
    /**
     * @param ProductInputDTO[] $productsInputDTO
     */
    public function execute(array $productsInputDTO, PurchaseOutputDTO $purchase): void
    {
        foreach ($productsInputDTO as $productInputDTO) {
            Product::create([
                'purchase_id' => $purchase->getId(),
                'name' => $productInputDTO->getName(),
                'quantity' => $productInputDTO->getQuantity(),
                'unit' => $productInputDTO->getUnit(),
                'unit_price' => $productInputDTO->getUnitPrice(),
                'total_price' => $productInputDTO->getTotalPrice()
            ]);
        }
    }
}
