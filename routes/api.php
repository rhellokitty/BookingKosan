<?php

use App\Http\Controllers\Api\BillPaymentController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\MidtransPaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('midtrans/callback', [MidtransController::class, 'callback']);
Route::get('spp/bills/{bill}', [BillPaymentController::class, 'show']);
Route::post('spp/bills/{bill}/payments/initiate', [BillPaymentController::class, 'initiate']);
Route::post('payments/midtrans/webhook', MidtransPaymentWebhookController::class);
