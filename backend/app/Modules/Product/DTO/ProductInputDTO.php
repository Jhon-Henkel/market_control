<?php

namespace App\Modules\Product\DTO;

readonly class ProductInputDTO
{
    public function __construct(
        private string $name,
        private string $quantity,
        private string $unit,
        private float $unitPrice,
        private float $totalPrice
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }
}
