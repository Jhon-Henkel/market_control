<?php

namespace App\Modules\ChatBot\UseCase\FinancesInHandsQuestionProcess;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\ChatBot\Enum\ResponseChatEnum;
use Illuminate\Support\Facades\Log;

class FinancesInHandsQuestionProcessUseCase
{
    public function execute(array $data, string $chatId): ResponseChatEnum
    {
        if (isset($data['callback_query'])) {
            $callbackQuery = $data['callback_query'];
            $callbackData = $callbackQuery['data'];
            ResponseChat::answerCallbackQuery($data['callback_query']['id']);

            $newText = ($callbackData === 'yes') ? "Você escolheu: Sim!" : "Você escolheu: Não!";
            Log::info($newText);

            ResponseChat::editMessage($chatId, $callbackQuery['message']['message_id'], $newText);

            if ($callbackData === 'yes') {
                // questionar qual a carteira, dando uma lista com as carteiras...
                ResponseChat::interactWithUser($chatId, "Marcando...");
                return ResponseChatEnum::Ok;
            } elseif ($callbackData === 'no') {
                ResponseChat::interactWithUser($chatId, "Operação cancelada.");
                return ResponseChatEnum::CancelOption;
            }
        }
        ResponseChat::interactWithUser($chatId, "Comando inválido. Digite /start para iniciar uma nova conversa.");
        return ResponseChatEnum::InvalidOption;
    }
}
