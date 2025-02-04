<?php

namespace App\Modules\_Shared\Response;

use App\Modules\ChatBot\Enum\ResponseChatEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ResponseChat
{
    public static function responseChat(ResponseChatEnum $status, int|string|null $chatId = null): JsonResponse
    {
        if ($status !== ResponseChatEnum::Ok && ! is_null($chatId)) {
            self::interactWithUser($chatId, "Chat Finalizado!");
        }
        return response()->json(['status' => $status->value]);
    }

    public static function interactWithUser(int|string $chatId, string $message, bool $yesOrNoCallback = false): void
    {
        $urlSendMessage = sprintf("https://api.telegram.org/bot%s/sendMessage", config('app.telegram_token'));
        $payload = ['chat_id' => $chatId, 'text' => $message];
        if ($yesOrNoCallback) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Sim', 'callback_data' => 'yes'],
                        ['text' => 'Não', 'callback_data' => 'no']
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ],
            ]);
        }
        Http::post($urlSendMessage, $payload);
    }

    public static function answerCallbackQuery($callbackId): void
    {
        $url = sprintf("https://api.telegram.org/bot%s/answerCallbackQuery", config('app.telegram_token'));
        Http::post($url, ['callback_query_id' => $callbackId]);
    }
}
