<?php

namespace App\Modules\ChatBot\UseCase\NfceStart;

use App\Modules\_Shared\Response\ResponseChat;
use Illuminate\Support\Facades\Log;

class NfceStartUseCase
{
    public function execute(string $chatId, string $cacheKey): void
    {
        Log::info('/nfce');
        $message = "Por favor, envie o link ou a foto do QR-Code da NFC-e. \nImportante a foto tem que ser somente do QR-Code, sem informações adicionais.";
        ResponseChat::interactWithUser($chatId, $message);
        cache([$cacheKey => 'waiting_nfce'], now()->addMinutes(5));
    }
}
