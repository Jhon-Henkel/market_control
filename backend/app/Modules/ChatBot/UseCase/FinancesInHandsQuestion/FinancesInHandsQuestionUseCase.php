<?php

namespace App\Modules\ChatBot\UseCase\FinancesInHandsQuestion;

use App\Modules\_Shared\Response\ResponseChat;
use Illuminate\Support\Facades\Log;

class FinancesInHandsQuestionUseCase
{
    public function execute(string $chatId, string $cacheKey): void
    {
        Log::info('Marcar no Finanças na mão?');
        cache([$cacheKey => 'finances_in_hands_question'], now()->addMinutes(5));
        $message = "Marcar uma saída do tipo mercado no Finanças na mão?";
        ResponseChat::interactWithUser($chatId, $message, true);
    }
}
