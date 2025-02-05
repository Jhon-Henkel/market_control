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
        $this->showToUser($lastPurchase, $chatId);
    }

    protected function showToUser(array $lastPurchase, string $chatId): void
    {
        $message = "📄 Resumo da compra: \n\n";
        $message .= "📅 Data da compra: " . new DateTime($lastPurchase['purchase']->purchase_date)->format('d/m/Y') . "\n";
        $message .= "🛒 Número de itens: " . $lastPurchase['purchase']->total_items . "\n";
        $message .= "💵 Valor do subtotal: R$ " . number_format($lastPurchase['purchase']->subtotal_value, 2, ',', '.') . "\n";
        $message .= "📊 Valor do desconto: R$ " . number_format($lastPurchase['purchase']->discount_value, 2, ',', '.') . "\n";
        $message .= "💰 Valor total: R$ " . number_format($lastPurchase['purchase']->total_value, 2, ',', '.') . "\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        $message .= "📝 Produtos da Compras:\n";

        ResponseChat::interactWithUser($chatId, $message);

        $productsPack = array_chunk($lastPurchase['products'], 30);
        foreach ($productsPack as $products) {
            ResponseChat::interactWithUser($chatId, $this->formatProducts($products));
        }

        ResponseChat::interactWithUser($chatId, "😅 Ufa, a lista acabou!\n");
    }

    protected function formatProducts(array $products): string
    {
        $return = '';
        foreach ($products as $product) {
            $name = substr($product->name, 0, 30);
            if (strlen($name) === 30) {
                $name .= "...";
            }
            $name = str_replace(' ', "\u{00A0}", $name);
            $value = number_format($product->total_value, 2, ',', '.');

            $return .= "🔹 $name\n          $product->quantity $product->unit - R$ $value";
            $return .= "          ━━━━━━━━━━━━━━━━━\n";
        }

        return $return;
    }
}
