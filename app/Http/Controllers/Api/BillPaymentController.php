<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;

class BillPaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
    ) {
    }

    public function show(Bill $bill): JsonResponse
    {
        $bill->load(['student', 'paymentTransactions' => fn ($query) => $query->latest('attempt_no')]);

        return response()->json([
            'data' => [
                'bill' => $bill,
                'payment_transactions' => $bill->paymentTransactions,
            ],
        ]);
    }

    public function initiate(Bill $bill): JsonResponse
    {
        $result = $this->paymentService->initiateBillPayment($bill);
        $paymentTransaction = $result['payment_transaction'];

        return response()->json([
            'message' => $result['already_paid']
                ? 'Bill has already been paid.'
                : ($result['reused_existing'] ? 'Reused existing payment attempt.' : 'Payment initiated successfully.'),
            'data' => [
                'already_paid' => $result['already_paid'],
                'reused_existing' => $result['reused_existing'],
                'bill' => $bill->fresh(),
                'payment_transaction' => $paymentTransaction,
            ],
        ], ($result['already_paid'] || $result['reused_existing']) ? 200 : 201);
    }
}
