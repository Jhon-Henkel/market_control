<?php

namespace App\Modules\FinancesInHands\UseCase\MarkMarketMovement;

use App\Modules\_Shared\Enum\Response\HttpStatusCodeEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinancesInHandsMarkMarketMovementUseCase
{
    public function execute(int $walletId, float $amount): array
    {
        $headers = ['MFP-TOKEN' => config('app.mfp.token')];
        $body = ['wallet_id' => $walletId, 'amount' => $amount];
        $response = Http::withHeaders($headers)->post(config('app.mfp.url') . 'market-control-app/movement', $body);
        Log::info("Status Code: {$response->status()}");
        if ($response->status() !== HttpStatusCodeEnum::HttpCreated->value) {
            Log::error("Erro ao registrar movimentação de mercado. Resposta: {$response->body()}");
            return ['status' => 'error'];
        }
        return ['status' => 'ok'];
    }
}
