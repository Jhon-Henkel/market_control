<?php

namespace App\Modules\ChatBot\UseCase\EndChat;

use App\Modules\_Shared\Response\ResponseChat;
use Illuminate\Support\Facades\Log;

class EndChatUseCase
{
    public function execute(string $chatId, string $cacheKey): void
    {
        Log::info('Conversa finalizada');
        ResponseChat::interactWithUser($chatId, "👋 Até mais!");
        cache()->forget($cacheKey);
    }
}
