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
        $endMessage = "Importante lembrar que o chat será finalizado após 5 minutos de inatividade.";
        ResponseChat::interactWithUser($chatId, $message . $this->getMenuOptions() . $endMessage);
    }

    protected function getMenuOptions(): string
    {
        return "
            /start -> Iniciar uma nova conversa
            /nfce -> Processar NFC-e
            /end -> Finalizar conversa\n";
    }
}
