<?php

namespace App\Modules\Purchase\DTO;

readonly class PurchaseInputDTO
{
    public function __construct(
        private int $totalItems,
        private int $nfceId,
        private float $subtotal,
        private float $discount,
        private float $amount,
        private string $purchaseDate,
    ) {
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getNfceId(): int
    {
        return $this->nfceId;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPurchaseDate(): string
    {
        return $this->purchaseDate;
    }
}
