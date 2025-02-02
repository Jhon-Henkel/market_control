<?php

namespace App\Modules\Nfce\DTO;

use App\Models\Nfce;

readonly class NfceOutputDTO
{
    private int $id;
    private string $number;
    private string $series;
    private string $key;
    private string $emission;

    public function __construct(Nfce $data)
    {
        $this->id = $data->id;
        $this->number = $data->number;
        $this->series = $data->series;
        $this->key = $data->key;
        $this->emission = $data->emission_date;
    }

    public function getId(): int
    {
        return $this->id;
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
