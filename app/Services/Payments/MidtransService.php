<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;

class MidtransService
{
    public function createSnapTransaction(PaymentTransaction $paymentTransaction): array
    {
        $this->configure();

        $bill = $paymentTransaction->bill;
        $student = $paymentTransaction->student;

        $params = [
            'transaction_details' => [
                'order_id' => $paymentTransaction->provider_order_id,
                'gross_amount' => $paymentTransaction->gross_amount,
            ],
            'customer_details' => [
                'first_name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone_number,
            ],
            'item_details' => [[
                'id' => $bill->bill_number,
                'price' => $bill->amount,
                'quantity' => 1,
                'name' => $bill->title,
            ]],
        ];

        $response = \Midtrans\Snap::createTransaction($params);

        return [
            'token' => $response->token ?? null,
            'redirect_url' => $response->redirect_url ?? null,
            'response' => json_decode(json_encode($response), true),
        ];
    }

    public function verifySignature(array $payload): bool
    {
        $serverKey = config('midtrans.serverKey');

        $expectedSignature = hash(
            'sha512',
            ($payload['order_id'] ?? '')
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . $serverKey
        );

        return hash_equals($expectedSignature, $payload['signature_key'] ?? '');
    }

    public function makeNotificationKey(array $payload): string
    {
        return hash('sha256', implode('|', [
            'midtrans',
            $payload['order_id'] ?? '',
            $payload['transaction_status'] ?? '',
            $payload['status_code'] ?? '',
            $payload['gross_amount'] ?? '',
            $payload['signature_key'] ?? '',
        ]));
    }

    private function configure(): void
    {
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');
    }
}
