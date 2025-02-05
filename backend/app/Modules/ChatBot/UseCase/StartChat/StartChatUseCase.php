<?php

namespace App\Modules\ChatBot\UseCase\StartChat;

use App\Modules\_Shared\Response\ResponseChat;
use Illuminate\Support\Facades\Log;

class StartChatUseCase
{
    /*
     * Opções do menu:
     *
     * start - Iniciar uma nova conversa
     * nfce - Processar NFC-e
     * month - Relatório do mês atual
     * last_purchase - Relatório da última compra
     * end - Finalizar conversa
     */
    public function execute(string $chatId, string $cacheKey): void
    {
        Log::info('/start');
        $message = "👋🏼👋🏼 Olá, bem-vindo ao Chatbot do Market Control.\n\nDigite / para ver as opções.\n \n";
        $endMessage = "⚠️⚠️Importante⚠️⚠️\nO chat será finalizado após 5 minutos de inatividade.";
        ResponseChat::interactWithUser($chatId, $message . $endMessage);
        cache()->forget($cacheKey);
    }
}
