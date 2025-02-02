<?php

use App\Modules\Nfce\Controller\NfceInsertController;
use Illuminate\Support\Facades\Route;

Route::post('/nfce', NfceInsertController::class);
