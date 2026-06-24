<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/midtrans/webhook', MidtransWebhookController::class)
    ->middleware(['throttle:webhook', 'midtrans_ip'])
    ->name('api.midtrans.webhook');
