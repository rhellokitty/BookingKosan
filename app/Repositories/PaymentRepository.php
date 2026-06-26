<?php

namespace App\Repositories;

use App\Interface\PaymentRepositoryInterface;
use App\Models\Bill;
use App\Models\PaymentTransaction;
use App\Support\PaymentTransactionStatus;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findBillForUpdate(int $billId): ?Bill
    {
        return Bill::query()
            ->with('student')
            ->lockForUpdate()
            ->find($billId);
    }

    public function findReusableAttemptForBill(int $billId): ?PaymentTransaction
    {
        return PaymentTransaction::query()
            ->where('bill_id', $billId)
            ->whereIn('status', PaymentTransactionStatus::reusableStatuses())
            ->latest('attempt_no')
            ->first();
    }

    public function getNextAttemptNumber(int $billId): int
    {
        return ((int) PaymentTransaction::query()
            ->where('bill_id', $billId)
            ->max('attempt_no')) + 1;
    }

    public function createPaymentTransaction(array $attributes): PaymentTransaction
    {
        return PaymentTransaction::create($attributes);
    }

    public function findPaymentByProviderOrderIdForUpdate(string $providerOrderId): ?PaymentTransaction
    {
        return PaymentTransaction::query()
            ->with(['bill', 'student'])
            ->where('provider_order_id', $providerOrderId)
            ->lockForUpdate()
            ->first();
    }
}
