<?php

namespace App\Modules\ChatBot\UseCase\FinancesInHandsWalletList;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\FinancesInHands\UseCase\GetWalletList\FinancesInHandsGetWalletList;

readonly class FinancesInHandsWalletListUseCase
{
    public function __construct(private FinancesInHandsGetWalletList $financesInHandsGetWalletList)
    {
    }

    public function execute(string $chatId, string $cacheKey): void
    {
        $wallets = $this->financesInHandsGetWalletList->execute();
        cache([$cacheKey => 'finances_in_hands_wallet_list'], now()->addMinutes(5));
        cache([$cacheKey . '_wallets' => json_encode($wallets)], now()->addMinutes(5));
        ResponseChat::interactWithUser($chatId, $this->makeWalletList($wallets));
    }

    protected function makeWalletList(array $wallets): string
    {
        if (empty($wallets)) {
            return "😞 Poxa, não encontrei nenhuma carteira 😞";
        }
        $walletList = "💰 Carteiras disponíveis:\n\n";
        foreach ($wallets as $wallet) {
            $walletList .= "{$wallet['id']} - {$wallet['name']}\n";
        }
        $walletList .= "\nAgora me fala, qual o número da carteira que quer usar?.";
        return $walletList;
    }
}
