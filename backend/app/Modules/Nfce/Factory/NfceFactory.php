<?php

namespace App\Modules\Nfce\Factory;

use App\Modules\Nfce\DTO\NfceInputDTO;

class NfceFactory
{
    public function makeInputDtoByArray(array $data): NfceInputDTO
    {
        return new NfceInputDTO(
            number: $data['number'],
            series: $data['series'],
            key: $data['key'],
            emission: $data['emission']
        );
    }
}
