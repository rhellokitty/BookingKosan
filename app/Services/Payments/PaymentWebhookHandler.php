<?php

namespace App\Services\Payments;

use App\Models\PaymentWebhookLog;
use App\Support\PaymentTransactionStatus;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PaymentWebhookHandler
{
    public function __construct(
        private MidtransService $midtransService,
        private PaymentStatusMapper $paymentStatusMapper,
        private BillPaymentService $billPaymentService,
    ) {
    }

    public function handleMidtransNotification(array $payload): array
    {
        $notificationKey = $this->midtransService->makeNotificationKey($payload);

        if (!$this->midtransService->verifySignature($payload)) {
            PaymentWebhookLog::firstOrCreate([
                'notification_key' => $notificationKey,
            ], [
                'provider' => 'midtrans',
                'provider_order_id' => $payload['order_id'] ?? null,
                'provider_status' => $payload['transaction_status'] ?? null,
                'signature_key' => $payload['signature_key'] ?? null,
                'payload' => $payload,
                'processing_result' => 'invalid_signature',
                'processed_at' => now(),
            ]);

            abort(401, 'Invalid signature key.');
        }

        try {
            return DB::transaction(function () use ($payload, $notificationKey) {
                $existingLog = PaymentWebhookLog::query()
                    ->where('notification_key', $notificationKey)
                    ->first();

                if ($existingLog) {
                    return [
                        'duplicate' => true,
                        'payment_transaction' => $existingLog->paymentTransaction,
                    ];
                }

                $paymentTransaction = \App\Models\PaymentTransaction::query()
                    ->with(['bill', 'student'])
                    ->where('provider_order_id', $payload['order_id'] ?? '')
                    ->lockForUpdate()
                    ->first();

                if (!$paymentTransaction) {
                    PaymentWebhookLog::firstOrCreate([
                        'notification_key' => $notificationKey,
                    ], [
                        'provider' => 'midtrans',
                        'provider_order_id' => $payload['order_id'] ?? null,
                        'provider_status' => $payload['transaction_status'] ?? null,
                        'signature_key' => $payload['signature_key'] ?? null,
                        'payload' => $payload,
                        'processing_result' => 'payment_not_found',
                        'processed_at' => now(),
                    ]);

                    abort(404, 'Payment transaction not found.');
                }

                if ((int) ($payload['gross_amount'] ?? 0) !== (int) $paymentTransaction->gross_amount) {
                    PaymentWebhookLog::create([
                        'payment_transaction_id' => $paymentTransaction->id,
                        'provider' => 'midtrans',
                        'notification_key' => $notificationKey,
                        'provider_order_id' => $payload['order_id'] ?? null,
                        'provider_status' => $payload['transaction_status'] ?? null,
                        'signature_key' => $payload['signature_key'] ?? null,
                        'payload' => $payload,
                        'processing_result' => 'amount_mismatch',
                        'processed_at' => now(),
                    ]);

                    abort(422, 'Gross amount mismatch.');
                }

                $incomingStatus = $this->paymentStatusMapper->mapMidtransStatus($payload);

                $log = PaymentWebhookLog::create([
                    'payment_transaction_id' => $paymentTransaction->id,
                    'provider' => 'midtrans',
                    'notification_key' => $notificationKey,
                    'provider_order_id' => $payload['order_id'] ?? null,
                    'provider_status' => $payload['transaction_status'] ?? null,
                    'signature_key' => $payload['signature_key'] ?? null,
                    'payload' => $payload,
                    'processing_result' => 'processing',
                ]);

                if (!$this->paymentStatusMapper->shouldApplyTransition($paymentTransaction->status, $incomingStatus)) {
                    $log->update([
                        'processing_result' => 'ignored_transition',
                        'processed_at' => now(),
                    ]);

                    return [
                        'duplicate' => false,
                        'ignored' => true,
                        'payment_transaction' => $paymentTransaction,
                    ];
                }

                $metadata = $paymentTransaction->metadata ?? [];
                $metadata['last_midtrans_payload'] = $payload;

                $updatePayload = [
                    'provider_transaction_id' => $payload['transaction_id'] ?? $paymentTransaction->provider_transaction_id,
                    'status' => $incomingStatus,
                    'provider_status' => $payload['transaction_status'] ?? $paymentTransaction->provider_status,
                    'payment_type' => $payload['payment_type'] ?? $paymentTransaction->payment_type,
                    'fraud_status' => $payload['fraud_status'] ?? $paymentTransaction->fraud_status,
                    'status_message' => $payload['status_message'] ?? $paymentTransaction->status_message,
                    'last_webhook_at' => now(),
                    'metadata' => $metadata,
                ];

                if ($incomingStatus === PaymentTransactionStatus::SETTLEMENT && !$paymentTransaction->paid_at) {
                    $updatePayload['paid_at'] = now();
                }

                if ($incomingStatus === PaymentTransactionStatus::EXPIRED && !$paymentTransaction->expired_at) {
                    $updatePayload['expired_at'] = now();
                }

                $paymentTransaction->update($updatePayload);
                $paymentTransaction->refresh();

                if ($paymentTransaction->status === PaymentTransactionStatus::SETTLEMENT) {
                    $this->billPaymentService->markBillAsPaid($paymentTransaction->bill, $paymentTransaction);
                }

                $log->update([
                    'processing_result' => 'processed',
                    'processed_at' => now(),
                ]);

                return [
                    'duplicate' => false,
                    'payment_transaction' => $paymentTransaction->fresh(['bill', 'student']),
                ];
            });
        } catch (QueryException $queryException) {
            if (in_array($queryException->getCode(), ['23000', '19'], true)) {
                return [
                    'duplicate' => true,
                    'payment_transaction' => null,
                ];
            }

            throw $queryException;
        }
    }
}
