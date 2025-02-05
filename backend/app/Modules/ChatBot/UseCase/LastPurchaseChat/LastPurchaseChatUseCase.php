<?php

namespace App\Modules\ChatBot\UseCase\LastPurchaseChat;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\Purchase\UseCase\GetLastPurchase\GetLastPurchaseUseCase;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $this->showToUser($chatId, $lastPurchase);
    }

    protected function showToUser(string $chatId, array $lastPurchase): void
    {
        $message = "📄 Resumo da última compra: \n\n";
        $message .= "📅 Data da compra: " . new DateTime($lastPurchase['purchase']->purchase_date)->format('d/m/Y') . "\n";
        $message .= "🛒 Número de itens: " . $lastPurchase['purchase']->total_items . "\n";
        $message .= "💵 Valor do subtotal: R$ " . number_format($lastPurchase['purchase']->subtotal_value, 2, ',', '.') . "\n";
        $message .= "📊 Valor do desconto: R$ " . number_format($lastPurchase['purchase']->discount_value, 2, ',', '.') . "\n";
        $message .= "💰 Valor total: R$ " . number_format($lastPurchase['purchase']->total_value, 2, ',', '.') . "\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        $message .= "📝 Produtos da Compras:\n\n";

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
        foreach ($products as $produto) {
            $name = substr($produto['name'], 0, 30);
            if (strlen($name) === 30) {
                $name .= "...";
            }
            $name = str_replace(' ', "\u{00A0}", Str::title(Str::lower($name)));
            $value = number_format($produto['total_price'], 2, ',', '.');

            $return .= "🔹 $name\n          $produto[quantity] $produto[unit] - R$ $value";
            $return .= "          ━━━━━━━━━━━━━━━━━\n";
        }
        return $return;
    }
}
