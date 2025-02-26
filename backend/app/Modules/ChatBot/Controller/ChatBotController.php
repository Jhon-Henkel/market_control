<?php

namespace App\Modules\ChatBot\Controller;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\ChatBot\Enum\ResponseChatEnum;
use App\Modules\ChatBot\UseCase\EndChat\EndChatUseCase;
use App\Modules\ChatBot\UseCase\FinancesInHandsMarkSpent\FinancesInHandsMarkSpentUseCase;
use App\Modules\ChatBot\UseCase\FinancesInHandsQuestion\FinancesInHandsQuestionUseCase;
use App\Modules\ChatBot\UseCase\FinancesInHandsQuestionProcess\FinancesInHandsQuestionProcessUseCase;
use App\Modules\ChatBot\UseCase\FinancesInHandsWalletSelect\FinancesInHandsWalletSelectUseCase;
use App\Modules\ChatBot\UseCase\LastPurchaseChat\LastPurchaseChatUseCase;
use App\Modules\ChatBot\UseCase\MonthChat\MonthChatUseCase;
use App\Modules\ChatBot\UseCase\NfceProcess\NfceProcessUseCase;
use App\Modules\ChatBot\UseCase\NfceStart\NfceStartUseCase;
use App\Modules\ChatBot\UseCase\StartChat\StartChatUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class ChatBotController
{
    public function __construct(
        private StartChatUseCase $startChatUseCase,
        private EndChatUseCase $endChatUseCase,
        private NfceStartUseCase $nfceStartUseCase,
        private NfceProcessUseCase $nfceProcessUseCase,
        private MonthChatUseCase $monthChatUseCase,
        private FinancesInHandsQuestionUseCase $financesInHandsQuestionUseCase,
        private FinancesInHandsQuestionProcessUseCase $financesInHandsQuestionProcessUseCase,
        private FinancesInHandsWalletSelectUseCase $financesInHandsWalletSelectUseCase,
        private FinancesInHandsMarkSpentUseCase $financesInHandsMarkSpentUseCase,
        private LastPurchaseChatUseCase $lastPurchaseChatUseCase
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            if (isset($data['callback_query'])) {
                $status = $this->processCallbackQuery($data);
                return ResponseChat::responseChat($status, $data['callback_query']['message']['chat']['id']);
            }

            if (! isset($data['message'])) {
                Log::info('Nenhuma mensagem recebida');
                return ResponseChat::responseChat(ResponseChatEnum::NoMessage);
            }

            $chatId = $data['message']['chat']['id'];

            $cacheKey = "telegram_{$chatId}_step";
            $step = cache($cacheKey, 'default');

            $message = $data['message']['text'] ?? '';
            if ($step !== 'waiting_nfce') {
                $message = strtolower($message);
            }

            $username = $data['message']['from']['username'] ?? 'Sem username';
            Log::info("Chat ID: {$chatId} - Mensagem: {$message} - Username: {$username}");

            if (!in_array($username, config('app.telegram_allowed_usernames'))) {
                Log::error("Usuário não autorizado: {$username}");
                return ResponseChat::responseChat(ResponseChatEnum::Unauthorized, $chatId);
            }

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
            }

            if ($message === '/last_purchase') {
                $this->lastPurchaseChatUseCase->execute($chatId);
                return ResponseChat::responseChat(ResponseChatEnum::FinishChat, $chatId);
            }

            if ($step === 'finances_in_hands_wallet_list') {
                $status = $this->financesInHandsWalletSelectUseCase->execute($chatId, $cacheKey, $message);
                if ($status === ResponseChatEnum::MfpWalletSelected) {
                    $status =  $this->financesInHandsMarkSpentUseCase->execute($chatId, $cacheKey, (int)$message);
                }
                return ResponseChat::responseChat($status, $chatId);
            }

            if ($message === '/month') {
                $this->monthChatUseCase->execute($chatId);
                return ResponseChat::responseChat(ResponseChatEnum::Ok);
            }

            ResponseChat::interactWithUser($chatId, "🚫 Comando inválido. Digite / para ver as opções.");
            return ResponseChat::responseChat(ResponseChatEnum::Ok);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            if (isset($chatId)) {
                $message = "🚫 Ocorreu um erro ao processar sua solicitação. Tente novamente.\n\n";
                $message .= "Erro: {$e->getMessage()}";
                ResponseChat::interactWithUser($chatId, $message);
            }
            return ResponseChat::responseChat(ResponseChatEnum::Ok);
        }
    }

    protected function stepWaitingNfce(array $data, string $chatId, string $cacheKey, string $message): ResponseChatEnum
    {
        $status = $this->nfceProcessUseCase->execute($data, $chatId, $cacheKey, $message);

        if ($status === ResponseChatEnum::InvalidUrl) {
            $this->nfceStartUseCase->execute($chatId, $cacheKey);
            return ResponseChatEnum::Ok;
        } elseif ($status === ResponseChatEnum::Ok || $status == ResponseChatEnum::NfceAlreadyProcessed) {
            $this->financesInHandsQuestionUseCase->execute($chatId, $cacheKey);
            return ResponseChatEnum::Ok;
        }
        return $status;
    }

    protected function processCallbackQuery(array $data): ResponseChatEnum
    {
        $chatId = $data['callback_query']['message']['chat']['id'];
        $cacheKey = "telegram_{$chatId}_step";
        $step = cache($cacheKey, 'default');

        if ($step === 'finances_in_hands_question') {
            return $this->financesInHandsQuestionProcessUseCase->execute($data, $chatId, $cacheKey);
        }

        return ResponseChatEnum::InvalidOption;
    }
}
