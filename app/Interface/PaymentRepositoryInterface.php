<?php

namespace App\Interface;

use App\Models\Bill;
use App\Models\PaymentTransaction;

interface PaymentRepositoryInterface
{
    public function findBillForUpdate(int $billId): ?Bill;

    public function findReusableAttemptForBill(int $billId): ?PaymentTransaction;

    public function getNextAttemptNumber(int $billId): int;

    public function createPaymentTransaction(array $attributes): PaymentTransaction;

    public function findPaymentByProviderOrderIdForUpdate(string $providerOrderId): ?PaymentTransaction;
}
