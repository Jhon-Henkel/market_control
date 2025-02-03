<?php

namespace App\Modules\ChatBot\Controller;

use App\Modules\Nfce\UseCase\InsertByChatbot\InsertByChatbotUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

readonly class ChatBotController
{
    public function __construct(private InsertByChatbotUseCase $insertByChatbotUseCase)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        Log::info('Iniciando Conversa');

        $data = $request->all();

        if (! isset($data['message'])) {
            return response()->json(['status' => 'no message']);
        }

        $chatId = $data['message']['chat']['id'];
        $message = strtolower($data['message']['text'] ?? '');
        $username = $data['message']['from']['username'] ?? 'Sem username';

        Log::info("Chat ID: {$chatId} - Mensagem: {$message} - Username: {$username}");

        if (!in_array($username, config('app.telegram_allowed_usernames'))) {
            Log::error("Usuário não autorizado: {$username}");
            return response()->json(['status' => 'unauthorized']);
        }

        $cacheKey = "telegram_{$chatId}_step";
        $step = cache($cacheKey, 'default');

        if ($message === '/start') {
            Log::info('/start');
            $this->interactWithUser($chatId, "Olá, bem-vindo ao Chatbot da NFC-e. Para começar, use um dos comandos disponíveis: \n**/nfce** -> Processar NFC-e");
            return response()->json(['status' => 'ok']);
        }

        if ($message === '/end') {
            Log::info('Conversa finalizada');
            $this->interactWithUser($chatId, "Até mais!");
            cache()->forget($cacheKey);
            return response()->json(['status' => 'ok']);
        }

        if ($message === '/nfce') {
            Log::info('/nfce');
            $this->interactWithUser($chatId, "Por favor, envie o link da NFC-e.");
            cache([$cacheKey => 'waiting_nfce'], now()->addMinutes(5));
        } elseif ($step === 'waiting_nfce') {
            if (!filter_var($message, FILTER_VALIDATE_URL)) {
                $this->interactWithUser($chatId, "O link enviado não parece ser válido. Por favor, envie um link correto.");
                return response()->json(['status' => 'invalid url']);
            }

            $result = $this->insertByChatbotUseCase->execute($message);

            if ($result['status'] === 'error') {
                $this->interactWithUser($chatId, "Ocorreu um erro ao processar a NFC-e. Por favor, tente novamente.");
                return response()->json(['status' => 'error to process nfce']);
            }

            $this->interactWithUser($chatId, "NFC-e processada com sucesso!");
            cache()->forget($cacheKey);
        }

        Log::info('Conversa finalizada');
        return response()->json(['status' => 'ok']);
    }

    protected function interactWithUser(int|string $chatId, string $message): void
    {
        $urlSendMessage = sprintf("https://api.telegram.org/bot%s/sendMessage", config('app.telegram_token'));
        Http::post($urlSendMessage, ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'MarkdownV2']);
    }
}
