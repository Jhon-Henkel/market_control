<?php

namespace App\Modules\ChatBot\Controller;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\ChatBot\Enum\ResponseChatEnum;
use App\Modules\ChatBot\UseCase\EndChat\EndChatUseCase;
use App\Modules\ChatBot\UseCase\FinancesInHandsQuestion\FinancesInHandsQuestionUseCase;
use App\Modules\ChatBot\UseCase\MonthChat\MonthChatUseCase;
use App\Modules\ChatBot\UseCase\NfceProcess\NfceProcessUseCase;
use App\Modules\ChatBot\UseCase\NfceStart\NfceStartUseCase;
use App\Modules\ChatBot\UseCase\StartChat\StartChatUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

readonly class ChatBotController
{
    public function __construct(
        private StartChatUseCase $startChatUseCase,
        private EndChatUseCase $endChatUseCase,
        private NfceStartUseCase $nfceStartUseCase,
        private NfceProcessUseCase $nfceProcessUseCase,
        private MonthChatUseCase $monthChatUseCase,
        private FinancesInHandsQuestionUseCase $financesInHandsQuestionUseCase,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->all();

        if (! isset($data['message'])) {
            Log::info('Nenhuma mensagem recebida');
            return ResponseChat::responseChat(ResponseChatEnum::NoMessage);
        }

        $chatId = $data['message']['chat']['id'];
        $message = strtolower($data['message']['text'] ?? '');
        $username = $data['message']['from']['username'] ?? 'Sem username';
        Log::info("Chat ID: {$chatId} - Mensagem: {$message} - Username: {$username}");

        if (!in_array($username, config('app.telegram_allowed_usernames'))) {
            Log::error("Usuário não autorizado: {$username}");
            return ResponseChat::responseChat(ResponseChatEnum::Unauthorized, $chatId);
        }

        $cacheKey = "telegram_{$chatId}_step";
        $step = cache($cacheKey, 'default');

        if ($message === '/start') {
            $this->startChatUseCase->execute($chatId, $cacheKey);
            return ResponseChat::responseChat(ResponseChatEnum::Ok);
        }

        if ($message === '/end') {
            $this->endChatUseCase->execute($chatId, $cacheKey);
            return ResponseChat::responseChat(ResponseChatEnum::Ok);
        }

        if ($message === '/nfce') {
            $this->nfceStartUseCase->execute($chatId, $cacheKey);
            return ResponseChat::responseChat(ResponseChatEnum::Ok);
        } elseif ($step === 'waiting_nfce') {
            $status = $this->stepWaitingNfce($data, $chatId, $cacheKey, $message);
            return ResponseChat::responseChat($status, $chatId);
        } elseif ($step === 'finances_in_hands_question') {
            $status = $this->statusFinancesInHandsQuestion($data, $chatId);
            return ResponseChat::responseChat($status, $chatId);
        }

        if ($message === '/month') {
            $this->monthChatUseCase->execute($chatId);
            return ResponseChat::responseChat(ResponseChatEnum::Ok);
        }

        ResponseChat::interactWithUser($chatId, "Comando inválido. Digite /start para iniciar uma nova conversa.");
        return ResponseChat::responseChat(ResponseChatEnum::Ok);
    }

    protected function stepWaitingNfce(array $data, string $chatId, string $cacheKey, string $message): ResponseChatEnum
    {
        $status = $this->nfceProcessUseCase->execute($data, $chatId, $cacheKey, $message);
        if ($status === ResponseChatEnum::InvalidUrl) {
            $this->nfceStartUseCase->execute($chatId, $cacheKey);
            return ResponseChatEnum::Ok;
        } elseif ($status === ResponseChatEnum::Ok) {
            $this->financesInHandsQuestionUseCase->execute($chatId, $cacheKey);
            return ResponseChatEnum::Ok;
        }
        return $status;
    }

    protected function statusFinancesInHandsQuestion(array $data, string $chatId): ResponseChatEnum
    {
        if (isset($data['callback_query'])) {
            $callbackQuery = $data['callback_query'];
            $callbackData = $callbackQuery['data'];
            ResponseChat::answerCallbackQuery($callbackQuery['id']);

            if ($callbackData === 'yes') {
                Log::info('Sim, marcar no Finanças na mão');
                ResponseChat::interactWithUser($callbackQuery['message']['chat']['id'], "Marcando...");
                return ResponseChatEnum::Ok;
            } elseif ($callbackData === 'no') {
                Log::info('Não, não marcar no Finanças na mão');
                ResponseChat::interactWithUser($callbackQuery['message']['chat']['id'], "Operação cancelada.");
                return ResponseChatEnum::CancelOption;
            }
        }
        ResponseChat::interactWithUser($chatId, "Comando inválido. Digite /start para iniciar uma nova conversa.");
        return ResponseChatEnum::InvalidOption;
    }
}
