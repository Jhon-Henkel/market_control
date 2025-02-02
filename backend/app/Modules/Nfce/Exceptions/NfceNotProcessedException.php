<?php

namespace App\Modules\Nfce\Exceptions;

use App\Models\Nfce;
use App\Modules\_Shared\Enum\Status\StatusYesNoEnum;
use App\Modules\_Shared\Exceptions\HttpExceptions\BadRequestException;

class NfceNotProcessedException extends BadRequestException
{
    public static int $nfceId;

    public function __construct()
    {
        parent::__construct('NFCe não processada');
    }

    public static function throwIfNotProcessed(Nfce $nfce): void
    {
        if ($nfce->processed != StatusYesNoEnum::YES->value) {
            self::$nfceId = $nfce->id;
            throw new self();
        }
    }
}
