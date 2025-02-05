<?php

namespace App\Modules\FinancesInHands\UseCase\GetWalletList;

use Illuminate\Support\Facades\Http;

class FinancesInHandsGetWalletList
{
    public function execute(): array
    {
        $headers = ['MFP-TOKEN' => config('app.mfp.token')];
        $response = Http::withHeaders($headers)->get(config('app.mfp.url') . 'wallets')->json();
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
