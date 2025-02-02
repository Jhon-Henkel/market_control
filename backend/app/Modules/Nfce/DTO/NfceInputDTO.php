<?php

namespace App\Modules\Nfce\DTO;

use App\Modules\_Shared\Enum\Date\DateFormatEnum;
use DateTime;

class NfceInputDTO
{
    public function __construct(
        private readonly string $number,
        private readonly string $series,
        private readonly string $key,
        private string $emission,
    ) {
        $this->emission = new DateTime($emission)->format(DateFormatEnum::AppDefault->value);
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getSeries(): string
    {
        return $this->series;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getEmission(): string
    {
        return $this->emission;
    }
}
