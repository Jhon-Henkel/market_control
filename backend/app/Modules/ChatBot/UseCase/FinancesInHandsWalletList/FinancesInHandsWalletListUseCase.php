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
        $ids = [];
        foreach ($wallets as $wallet) {
            $ids[] = $wallet['id'];
        }
        cache($cacheKey, 'finances_in_hands_wallet_list');
        cache($cacheKey . '_wallets', json_encode($ids));
        ResponseChat::interactWithUser($chatId, $this->makeWalletList($wallets));
    }

    protected function makeWalletList(array $wallets): string
    {
        if (empty($wallets)) {
            return "Nenhuma carteira encontrada.";
        }
        $walletList = "Carteiras disponíveis:\n\n";
        foreach ($wallets as $wallet) {
            $walletList .= "{$wallet['id']} - Nome: {$wallet['name']}\n";
        }
        $walletList .= "\n\nDigite o número da carteira que deseja usar.";
        return $walletList;
    }
}
