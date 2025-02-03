<?php

namespace App\Modules\ChatBot\Controller;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\ChatBot\Enum\ResponseChatEnum;
use App\Modules\ChatBot\UseCase\EndChat\EndChatUseCase;
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
        private NfceProcessUseCase $nfceProcessUseCase
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        Log::info('Iniciando Conversa');

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
            $this->startChatUseCase->execute($chatId);
            return ResponseChat::responseChat(ResponseChatEnum::Ok);
        }

        if ($message === '/end') {
            $this->endChatUseCase->execute($chatId, $cacheKey);
            return ResponseChat::responseChat(ResponseChatEnum::Ok);
        }

        if ($message === '/nfce') {
            $this->nfceStartUseCase->execute($chatId, $cacheKey);
        } elseif ($step === 'waiting_nfce') {
            $status = $this->nfceProcessUseCase->execute($data, $chatId, $cacheKey, $message);
            return ResponseChat::responseChat($status, $chatId);
        }

        ResponseChat::interactWithUser($chatId, "Comando inválido. Digite /start para iniciar uma nova conversa.");
        Log::info('Conversa finalizada');
        return ResponseChat::responseChat(ResponseChatEnum::Ok);
    }
}
