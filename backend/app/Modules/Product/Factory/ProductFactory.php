<?php

namespace App\Modules\Product\Factory;

use App\Modules\Product\DTO\ProductInputDTO;

class ProductFactory
{
    public function makeInputDtoByArray(array $products): array
    {
        $output = [];
        foreach ($products as $product) {
            $output[] = new ProductInputDTO(
                name: $product['name'],
                quantity: $product['quantity'],
                unit: $product['unit'],
                unitPrice: $product['unit_price'],
                totalPrice: $product['total_price']
            );
        }
        return $output;
    }
}
