<?php

namespace App\Services\Payments;

use App\Interface\PaymentRepositoryInterface;
use App\Models\Bill;
use App\Models\PaymentTransaction;
use App\Support\PaymentTransactionStatus;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private MidtransService $midtransService,
    ) {
    }

    public function initiateBillPayment(Bill $bill): array
    {
        try {
            return Cache::lock("bill-payment-initiate:{$bill->id}", 10)->block(5, function () use ($bill) {
                $flags = [
                    'already_paid' => false,
                    'reused_existing' => false,
                ];

                $paymentTransaction = DB::transaction(function () use ($bill, &$flags) {
                    $lockedBill = $this->paymentRepository->findBillForUpdate($bill->id);

                    if (!$lockedBill) {
                        abort(404, 'Bill not found.');
                    }

                    if ($lockedBill->isPaid()) {
                        $flags['already_paid'] = true;

                        return $lockedBill->paymentTransactions()
                            ->where('status', PaymentTransactionStatus::SETTLEMENT)
                            ->latest('attempt_no')
                            ->first()
                            ?? $lockedBill->paymentTransactions()->latest('attempt_no')->first();
                    }

                    $existingAttempt = $this->paymentRepository->findReusableAttemptForBill($lockedBill->id);

                    if ($existingAttempt) {
                        $flags['reused_existing'] = true;

                        return $existingAttempt->load(['bill', 'student']);
                    }

                    return $this->paymentRepository->createPaymentTransaction([
                        'bill_id' => $lockedBill->id,
                        'student_id' => $lockedBill->student_id,
                        'provider' => 'midtrans',
                        'code' => $this->generatePaymentCode(),
                        'provider_order_id' => $this->generateProviderOrderId(),
                        'attempt_no' => $this->paymentRepository->getNextAttemptNumber($lockedBill->id),
                        'gross_amount' => $lockedBill->amount,
                        'status' => PaymentTransactionStatus::CREATING,
                        'initiated_at' => now(),
                    ])->load(['bill', 'student']);
                });

                if (!$paymentTransaction instanceof PaymentTransaction) {
                    return $flags + ['payment_transaction' => null];
                }

                if ($flags['already_paid']) {
                    return $flags + ['payment_transaction' => $paymentTransaction];
                }

                if ($paymentTransaction->snap_token && $paymentTransaction->snap_redirect_url) {
                    return $flags + ['payment_transaction' => $paymentTransaction];
                }

                try {
                    $midtransResponse = $this->midtransService->createSnapTransaction($paymentTransaction);

                    $metadata = $paymentTransaction->metadata ?? [];
                    $metadata['midtrans_create_response'] = $midtransResponse['response'];

                    $paymentTransaction->update([
                        'status' => PaymentTransactionStatus::INITIATED,
                        'snap_token' => $midtransResponse['token'],
                        'snap_redirect_url' => $midtransResponse['redirect_url'],
                        'metadata' => $metadata,
                    ]);

                    return $flags + ['payment_transaction' => $paymentTransaction->fresh(['bill', 'student'])];
                } catch (\Throwable $throwable) {
                    $paymentTransaction->update([
                        'status' => PaymentTransactionStatus::FAILED,
                        'status_message' => 'Failed to create Midtrans transaction.',
                        'metadata' => array_merge($paymentTransaction->metadata ?? [], [
                            'midtrans_create_error' => $throwable->getMessage(),
                        ]),
                    ]);

                    throw $throwable;
                }
            });
        } catch (LockTimeoutException $exception) {
            abort(409, 'Another payment initiation is still in progress for this bill.');
        }
    }

    private function generatePaymentCode(): string
    {
        return 'PAY-' . Str::upper((string) Str::ulid());
    }

    private function generateProviderOrderId(): string
    {
        return 'SPP-' . Str::upper((string) Str::ulid());
    }
}
