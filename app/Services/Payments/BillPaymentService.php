<?php

namespace App\Services\Payments;

use App\Models\Bill;
use App\Models\PaymentTransaction;
use App\Support\BillStatus;

class BillPaymentService
{
    public function markBillAsPaid(Bill $bill, PaymentTransaction $paymentTransaction): Bill
    {
        $bill = Bill::query()->lockForUpdate()->findOrFail($bill->id);

        if ($bill->isPaid()) {
            return $bill;
        }

        $metadata = $bill->metadata ?? [];
        $metadata['paid_via_payment_transaction_id'] = $paymentTransaction->id;

        $bill->update([
            'status' => BillStatus::PAID,
            'paid_at' => $paymentTransaction->paid_at ?? now(),
            'metadata' => $metadata,
        ]);

        return $bill->fresh();
    }
}
