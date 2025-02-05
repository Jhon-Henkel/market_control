<?php

namespace App\Modules\ChatBot\UseCase\FinancesInHandsQuestionProcess;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\ChatBot\Enum\ResponseChatEnum;
use App\Modules\ChatBot\UseCase\FinancesInHandsWalletList\FinancesInHandsWalletListUseCase;
use Illuminate\Support\Facades\Log;

readonly class FinancesInHandsQuestionProcessUseCase
{
    public function __construct(private FinancesInHandsWalletListUseCase $financesInHandsWalletList)
    {
    }

    public function execute(array $data, string $chatId, string $cacheKey): ResponseChatEnum
    {
        if (isset($data['callback_query'])) {
            $callbackQuery = $data['callback_query'];
            $callbackData = $callbackQuery['data'];
            ResponseChat::answerCallbackQuery($data['callback_query']['id']);

            $newText = ($callbackData === 'yes') ? "Você escolheu: Sim!" : "Você escolheu: Não!";
            Log::info($newText);

            ResponseChat::editMessage($chatId, $callbackQuery['message']['message_id'], $newText);

            if ($callbackData === 'yes') {
                ResponseChat::interactWithUser($chatId, "Buscando Carteiras...");
                $this->financesInHandsWalletList->execute($chatId, $cacheKey);
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
