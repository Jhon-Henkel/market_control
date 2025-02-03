<?php

namespace App\Modules\ChatBot\UseCase\MonthChat;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\Purchase\UseCase\GetThisMonthPurchases\GetThisMonthPurchasesUseCase;
use Illuminate\Support\Facades\Log;

readonly class MonthChatUseCase
{
    public function __construct(private GetThisMonthPurchasesUseCase $getThisMonthPurchasesUseCase)
    {
    }

    public function execute(string $chatId): void
    {
        Log::info('/month');
        $purchases = $this->getThisMonthPurchasesUseCase->execute();
        ResponseChat::interactWithUser($chatId, $this->formatToUser($purchases));
        ResponseChat::interactWithUser($chatId, "Chat Finalizado!");
    }

    protected function formatToUser(array $purchases): string
    {
        $totalAmount = number_format($purchases['total_amount'], 2, ',', '.');

        $message = "📊 **Resumo das Compras do Mês**\n";
        $message .= "🛒 **Total de Itens:** $purchases[total_products]\n";
        $message .= "💰 **Valor Total:** R$ $totalAmount\n\n";
        $message .= "📝 **Lista de Compras:**\n";

        foreach ($purchases['products'] as $produto) {
            $quantity = $produto['quantity'];
            $unit = $produto['unit'];
            $name = $produto['name'];
            $value = number_format($produto['total_value'], 2, ',', '.');

            $message .= "🔹 $quantity $unit - R$ $value - <b>$name</b><br>";
        }

        return $message;
    }
}
