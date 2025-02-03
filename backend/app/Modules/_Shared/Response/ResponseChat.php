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

    public static function interactWithUser(int|string $chatId, string $message): void
    {
        $urlSendMessage = sprintf("https://api.telegram.org/bot%s/sendMessage", config('app.telegram_token'));
        Http::post($urlSendMessage, ['chat_id' => $chatId, 'text' => $message]);
    }
}
