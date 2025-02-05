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
        $this->showToUser($chatId, $purchases);
        ResponseChat::interactWithUser($chatId, "✅ Chat Finalizado!");
    }

    protected function showToUser(string $chatId, array $purchases): void
    {
        if (empty($purchases)) {
            ResponseChat::interactWithUser($chatId, "Nenhuma compra realizada este mês.");
            return;
        }

        $totalAmount = number_format($purchases['total_amount'], 2, ',', '.');

        $message = "📊 Resumo das Compras Deste Mês\n\n";
        $message .= "🛒 Total de Itens: $purchases[total_products]\n";
        $message .= "💰 Valor Total: R$ $totalAmount\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        $message .= "📝 Lista de Compras:\n\n";

        ResponseChat::interactWithUser($chatId, $message);

        $productsPack = array_chunk($purchases['products'], 30);
        foreach ($productsPack as $products) {
            ResponseChat::interactWithUser($chatId, $this->formatProducts($products));
        }

        ResponseChat::interactWithUser($chatId, "😅 Ufa, a lista acabou!\n");
    }

    protected function formatProducts(array $products): string
    {
        $message = '';
        foreach ($products as $produto) {
            $quantity = $produto['quantity'];
            $unit = $produto['unit'];
            $name = substr($produto['name'], 0, 30);
            if (strlen($name) === 30) {
                $name .= "...";
            }
            $name = str_replace(' ', "\u{00A0}", $name);
            $value = number_format($produto['total_value'], 2, ',', '.');

            $message .= "🔹 $name\n          $quantity $unit - R$ $value";
            $message .= "          ━━━━━━━━━━━━━━━━━\n";
        }

        return $message;
    }
}
