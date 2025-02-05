<?php

namespace App\Modules\FinancesInHands\UseCase\GetWalletList;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinancesInHandsGetWalletList
{
    public function execute(): array
    {
        Log::info('Solicitando lista de carteiras');
        $headers = ['MFP-TOKEN' => config('app.mfp.token')];
        $response = Http::withHeaders($headers)->get(config('app.mfp.url') . 'market-control-app/wallets');
        Log::info("Status Code: {$response->status()} - Body: {$response->body()}");
        $response = $response->json();
        if (empty($response)) {
            return [];
        }
        $result = [];
        foreach ($response as $wallet) {
            $result[] = [
                'id' => $wallet['id'],
                'name' => $wallet['name'],
            ];
        }
        return $result;
    }
}
