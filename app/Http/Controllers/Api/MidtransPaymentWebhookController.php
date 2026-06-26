<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentWebhookHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransPaymentWebhookController extends Controller
{
    public function __construct(
        private PaymentWebhookHandler $paymentWebhookHandler,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->paymentWebhookHandler->handleMidtransNotification($request->all());

        return response()->json([
            'message' => $result['duplicate'] ? 'Duplicate notification ignored.' : 'Notification processed.',
            'data' => [
                'duplicate' => $result['duplicate'],
                'ignored' => $result['ignored'] ?? false,
                'payment_transaction' => $result['payment_transaction'],
            ],
        ]);
    }
}
