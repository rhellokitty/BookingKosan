<?php

namespace App\Services\Payments;

use App\Support\PaymentTransactionStatus;

class PaymentStatusMapper
{
    public function mapMidtransStatus(array $payload): string
    {
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if ($transactionStatus === 'capture') {
            if ($paymentType === 'credit_card' && $fraudStatus === 'challenge') {
                return PaymentTransactionStatus::CHALLENGE;
            }

            return PaymentTransactionStatus::SETTLEMENT;
        }

        return match ($transactionStatus) {
            'settlement' => PaymentTransactionStatus::SETTLEMENT,
            'pending' => PaymentTransactionStatus::PENDING,
            'deny', 'failure' => PaymentTransactionStatus::FAILED,
            'expire' => PaymentTransactionStatus::EXPIRED,
            'cancel' => PaymentTransactionStatus::CANCELLED,
            default => PaymentTransactionStatus::UNKNOWN,
        };
    }

    public function shouldApplyTransition(string $currentStatus, string $incomingStatus): bool
    {
        if ($incomingStatus === PaymentTransactionStatus::UNKNOWN) {
            return false;
        }

        if ($currentStatus === PaymentTransactionStatus::SETTLEMENT) {
            return $incomingStatus === PaymentTransactionStatus::SETTLEMENT;
        }

        if (in_array($currentStatus, [
            PaymentTransactionStatus::FAILED,
            PaymentTransactionStatus::EXPIRED,
            PaymentTransactionStatus::CANCELLED,
        ], true)) {
            return in_array($incomingStatus, [
                PaymentTransactionStatus::FAILED,
                PaymentTransactionStatus::EXPIRED,
                PaymentTransactionStatus::CANCELLED,
                PaymentTransactionStatus::SETTLEMENT,
            ], true);
        }

        return true;
    }
}
