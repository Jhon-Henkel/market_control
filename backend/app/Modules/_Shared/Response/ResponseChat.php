<?php

namespace App\Modules\_Shared\Response;

use App\Modules\ChatBot\Enum\ResponseChatEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ResponseChat
{
    protected static function getUrl(string $endpoint): string
    {
        return sprintf("https://api.telegram.org/bot%s/" . $endpoint, config('app.telegram_token'));
    }

    public static function responseChat(ResponseChatEnum $status, int|string|null $chatId = null): JsonResponse
    {
        if ($status !== ResponseChatEnum::Ok && ! is_null($chatId)) {
            self::interactWithUser($chatId, "Chat Finalizado!");
        }
        return response()->json(['status' => $status->value]);
    }

    public static function interactWithUser(int|string $chatId, string $message, bool $yesOrNoCallback = false): void
    {
        $payload = ['chat_id' => $chatId, 'text' => $message];
        if ($yesOrNoCallback) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Sim', 'callback_data' => 'yes'],
                        ['text' => 'Não', 'callback_data' => 'no']
                    ]
                ],
            ]);
        }
        Http::post(self::getUrl('sendMessage'), $payload);
    }

    public static function editMessage($chatId, $messageId, $newText): void
    {
        Http::post(self::getUrl('editMessageText'), [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $newText,
            'reply_markup' => json_encode(['inline_keyboard' => []])
        ]);
    }

    public static function answerCallbackQuery($callbackId): void
    {
        Http::post(self::getUrl('answerCallbackQuery'), ['callback_query_id' => $callbackId]);
    }
}
