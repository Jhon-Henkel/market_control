<?php

use App\Modules\ChatBot\Controller\ChatBotController;
use Illuminate\Support\Facades\Route;

Route::post('/chat/webhook', ChatBotController::class);
