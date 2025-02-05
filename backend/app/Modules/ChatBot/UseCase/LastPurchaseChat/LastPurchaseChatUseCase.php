<?php

namespace App\Modules\ChatBot\UseCase\LastPurchaseChat;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\Purchase\UseCase\GetLastPurchase\GetLastPurchaseUseCase;
use DateTime;
use Illuminate\Support\Facades\Log;

readonly class LastPurchaseChatUseCase
{
    public function __construct(private GetLastPurchaseUseCase $getLastPurchaseUseCase)
    {
    }

    public function execute(string $chatId): void
    {
        Log::info('Buscando últimas compras...');
        $lastPurchase = $this->getLastPurchaseUseCase->execute();
        if (empty($lastPurchase['purchase'])) {
            ResponseChat::interactWithUser($chatId, '😞 Poxa, não encontrei nenhuma compra 😞');
        }
        ResponseChat::interactWithUser($chatId, $this->formatToUser($lastPurchase));
    }

    protected function formatToUser(array $lastPurchase): string
    {
        $message = "📄 Resumo da compra: \n\n";
        $message .= "📅 Data da compra: " . new DateTime($lastPurchase['purchase']->purchase_date)->format('d/m/Y') . "\n";
        $message .= "🛒 Número de itens: " . $lastPurchase['purchase']->total_items . "\n";
        $message .= "💵 Valor do subtotal: R$ " . number_format($lastPurchase['purchase']->subtotal_value, 2, ',', '.') . "\n";
        $message .= "📊 Valor do desconto: R$ " . number_format($lastPurchase['purchase']->discount_value, 2, ',', '.') . "\n";
        $message .= "💰 Valor total: R$ " . number_format($lastPurchase['purchase']->total_value, 2, ',', '.') . "\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        $message .= "📝 Produtos da Compras:\n\n";

        return $message;
        foreach ($lastPurchase['products'] as $produto) {
            $name = substr($produto->name, 0, 30);
            if (strlen($name) === 30) {
                $name .= "...";
            }
            $name = str_replace(' ', "\u{00A0}", $name);
            $value = number_format($produto->total_value, 2, ',', '.');

            $message .= "🔹 $name\n          $produto->quantity $produto->unit - R$ $value";
            $message .= "          ━━━━━━━━━━━━━━━━━\n";
        }
        $message .= "😅 Ufa, a lista acabou!\n";

        return $message;
    }
}
