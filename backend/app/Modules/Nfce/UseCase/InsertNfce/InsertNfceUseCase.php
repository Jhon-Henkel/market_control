<?php

namespace App\Modules\Nfce\UseCase\InsertNfce;

use App\Models\Nfce;
use App\Modules\Nfce\DTO\NfceInputDTO;
use App\Modules\Nfce\DTO\NfceOutputDTO;

class InsertNfceUseCase
{
    public function execute(NfceInputDTO $input): NfceOutputDTO
    {
        $nfce =  Nfce::create([
            'number' => $input->getNumber(),
            'series' => $input->getSeries(),
            'key' => $input->getKey(),
            'emission_date' => $input->getEmission(),
        ]);
        return new NfceOutputDTO($nfce);
    }
}
