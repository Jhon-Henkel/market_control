<?php

use App\Modules\ChatBot\Controller\ChatBotController;
use App\Modules\Nfce\Controller\NfceInsertController;
use Illuminate\Support\Facades\Route;

//Route::post('/nfce', NfceInsertController::class);
Route::post('/chat/webhook', ChatBotController::class);
