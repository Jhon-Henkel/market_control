<?php

namespace App\Modules\Purchase\DTO;

use App\Models\Purchase;

readonly class PurchaseOutputDTO
{
    private int $id;
    private int $totalItems;
    private int $nfceId;
    private float $subtotal;
    private float $discount;
    private float $amount;
    private string $purchaseDate;

    public function __construct(PurchaseInputDTO $input, Purchase $purchase)
    {
        $this->id = $purchase->id;
        $this->totalItems = $input->getTotalItems();
        $this->nfceId = $input->getNfceId();
        $this->subtotal = $input->getSubtotal();
        $this->discount = $input->getDiscount();
        $this->amount = $input->getAmount();
        $this->purchaseDate = $input->getPurchaseDate();
    }

    public function getId(): int
    {
        return $this->id;
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
