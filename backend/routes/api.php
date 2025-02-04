<?php

use App\Modules\ChatBot\Controller\ChatBotController;
use Illuminate\Support\Facades\Route;

Route::post('/chat/webhook', ChatBotController::class);

Route::get('/', function () {
    $useCase = new \App\Modules\Purchase\UseCase\GetThisMonthPurchases\GetThisMonthPurchasesUseCase();
    $teste = $useCase->execute();
    dd($teste);
    return response()->json(['message' => 'Hello World!']);
});
