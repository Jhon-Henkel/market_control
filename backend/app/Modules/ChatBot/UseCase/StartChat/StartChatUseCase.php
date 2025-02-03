<?php

namespace App\Modules\ChatBot\UseCase\StartChat;

use App\Modules\_Shared\Response\ResponseChat;
use Illuminate\Support\Facades\Log;

class StartChatUseCase
{
    public function execute(string $chatId): void
    {
        Log::info('/start');
        $message = "Olá, bem-vindo ao Chatbot do Market Control. Para começar, use um dos comandos disponíveis: \n";
        ResponseChat::interactWithUser($chatId, $message . $this->getMenuOptions());
    }

    protected function getMenuOptions(): string
    {
        return "
            \n/nfce -> Processar NFC-e
            \n/end -> Finalizar conversa
        ";
    }
}
