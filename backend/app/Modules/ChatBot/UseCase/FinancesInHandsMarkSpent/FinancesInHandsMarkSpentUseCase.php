<?php

namespace App\Modules\ChatBot\UseCase\FinancesInHandsMarkSpent;

use App\Models\Purchase;
use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\ChatBot\Enum\ResponseChatEnum;
use App\Modules\FinancesInHands\UseCase\MarkMarketMovement\FinancesInHandsMarkMarketMovementUseCase;
use Illuminate\Support\Facades\Log;

readonly class FinancesInHandsMarkSpentUseCase
{
    public function __construct(private FinancesInHandsMarkMarketMovementUseCase $financesInHandsMarkMarketMovementUseCase)
    {
    }

    public function execute(string $chatId, string $cacheKey, int $walletId): ResponseChatEnum
    {
        Log::info('Solicitando registro de movimentação de mercado');
        $purchase = Purchase::orderBy('id', 'desc')->first();
        $result = $this->financesInHandsMarkMarketMovementUseCase->execute($walletId, $purchase->total_value);
        if ($result['status'] === 'error') {
            $message = "Sinto muito ! 😞😞 \n\n Não foi possível registrar a movimentação de mercado. \nPor favor, registre manualmente no App Finanças na Mão.";
            ResponseChat::interactWithUser($chatId, $message);
            return ResponseChatEnum::MfpErrorToRegisterPurchase;
        }

        $message = "Movimentação de mercado registrada com sucesso! 🎉🎉\n\n";
        $message .= "📄 Resumo da compra: \n\n";
        $message .= "📅 Data da compra: " . $purchase->purchase_date . "\n";
        $message .= "🛒 Número de itens: " . $purchase->total_items . "\n";
        $message .= "💸 Carteira: " . $this->getWalletName($cacheKey, $walletId) . "\n";
        $message .= "💵 Valor do subtotal: R$ " . number_format($purchase->subtotal_value, 2, ',', '.') . "\n";
        $message .= "📊 Valor do desconto: R$ " . number_format($purchase->discount_value, 2, ',', '.') . "\n";
        $message .= "💰 Valor total: R$ " . number_format($purchase->total_value, 2, ',', '.') . "\n";

        cache()->forget($cacheKey);
        cache()->forget($cacheKey . '_wallets');

        Log::info("Movimentação de mercado registrada com sucesso");

        ResponseChat::interactWithUser($chatId, $message);
        return ResponseChatEnum::FinishChat;
    }

    protected function getWalletName(string $cacheKey, int $walletId): string
    {
        $wallets = json_decode(cache($cacheKey . '_wallets'), true);
        foreach ($wallets as $wallet) {
            if ($wallet['id'] === $walletId) {
                return $wallet['name'];
            }
        }
        return '';
    }
}
