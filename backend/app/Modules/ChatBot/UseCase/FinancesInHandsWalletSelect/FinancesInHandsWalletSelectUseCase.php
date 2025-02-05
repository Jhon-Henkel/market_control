<?php

namespace App\Modules\ChatBot\UseCase\FinancesInHandsWalletSelect;

use App\Modules\_Shared\Response\ResponseChat;
use App\Modules\ChatBot\Enum\ResponseChatEnum;
use App\Modules\ChatBot\UseCase\FinancesInHandsWalletList\FinancesInHandsWalletListUseCase;
use Illuminate\Support\Facades\Log;

readonly class FinancesInHandsWalletSelectUseCase
{
    public function __construct(private FinancesInHandsWalletListUseCase $financesInHandsWalletList)
    {
    }

    public function execute(string $chatId, string $cacheKey, string $message): ResponseChatEnum
    {
        Log::info('Finanças na mão - Carteira Selecionada');
        $message = (int)$message;
        $wallets = json_decode(cache($cacheKey . '_wallets'), true);
        if (! $this->isValidChoice($message, $wallets, $chatId)) {
            $this->financesInHandsWalletList->execute($chatId, $cacheKey);
            return ResponseChatEnum::Ok;
        }
        Log::info('Carteira selecionada: ' . $message);
        ResponseChat::interactWithUser($chatId, 'Carteira selecionada: ' . $this->getWalletName($wallets, $message));
        return ResponseChatEnum::MfpWalletSelected;
    }

    protected function isValidChoice(int $choice, array $wallets, string $chatId): bool
    {
        $ids = array_map(function ($wallet) { return $wallet['id']; }, $wallets);
        if (! in_array($choice, $ids)) {
            ResponseChat::interactWithUser($chatId, "⚠️⚠️ Carteira inválida.\n\nSerá que selecionou a correta?.");
            return false;
        }
        return true;
    }

    protected function getWalletName(array $wallets, int $selectedId): string
    {
        $walletName = '';
        foreach ($wallets as $wallet) {
            if ($wallet['id'] == $selectedId) {
                $walletName = $wallet['name'];
                break;
            }
        }
        return $walletName;
    }
}
