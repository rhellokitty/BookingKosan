<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\PaymentTransaction;
use App\Models\Student;
use App\Services\Payments\MidtransService;
use App\Support\BillStatus;
use App\Support\PaymentTransactionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_initiates_payment_for_an_unpaid_bill(): void
    {
        $bill = $this->createBill();

        $this->mock(MidtransService::class, function ($mock): void {
            $mock->shouldReceive('createSnapTransaction')
                ->once()
                ->andReturn([
                    'token' => 'snap-token-1',
                    'redirect_url' => 'https://snap.midtrans.test/pay/1',
                    'response' => ['token' => 'snap-token-1'],
                ]);
        });

        $response = $this->postJson("/api/spp/bills/{$bill->id}/payments/initiate");

        $response->assertCreated()
            ->assertJsonPath('data.payment_transaction.snap_token', 'snap-token-1')
            ->assertJsonPath('data.payment_transaction.status', PaymentTransactionStatus::INITIATED);

        $this->assertDatabaseHas('payment_transactions', [
            'bill_id' => $bill->id,
            'attempt_no' => 1,
            'status' => PaymentTransactionStatus::INITIATED,
        ]);
    }

    public function test_it_reuses_existing_open_attempt(): void
    {
        $bill = $this->createBill();

        PaymentTransaction::create([
            'bill_id' => $bill->id,
            'student_id' => $bill->student_id,
            'provider' => 'midtrans',
            'code' => 'PAY-EXISTING',
            'provider_order_id' => 'SPP-EXISTING',
            'attempt_no' => 1,
            'gross_amount' => $bill->amount,
            'status' => PaymentTransactionStatus::INITIATED,
            'snap_token' => 'snap-existing',
            'snap_redirect_url' => 'https://snap.midtrans.test/existing',
            'initiated_at' => now(),
        ]);

        $this->mock(MidtransService::class, function ($mock): void {
            $mock->shouldNotReceive('createSnapTransaction');
        });

        $response = $this->postJson("/api/spp/bills/{$bill->id}/payments/initiate");

        $response->assertOk()
            ->assertJsonPath('data.reused_existing', true)
            ->assertJsonPath('data.payment_transaction.snap_token', 'snap-existing');

        $this->assertDatabaseCount('payment_transactions', 1);
    }

    public function test_retry_after_failed_attempt_creates_new_attempt(): void
    {
        $bill = $this->createBill();

        PaymentTransaction::create([
            'bill_id' => $bill->id,
            'student_id' => $bill->student_id,
            'provider' => 'midtrans',
            'code' => 'PAY-FAILED',
            'provider_order_id' => 'SPP-FAILED',
            'attempt_no' => 1,
            'gross_amount' => $bill->amount,
            'status' => PaymentTransactionStatus::FAILED,
            'initiated_at' => now(),
        ]);

        $this->mock(MidtransService::class, function ($mock): void {
            $mock->shouldReceive('createSnapTransaction')
                ->once()
                ->andReturn([
                    'token' => 'snap-token-2',
                    'redirect_url' => 'https://snap.midtrans.test/pay/2',
                    'response' => ['token' => 'snap-token-2'],
                ]);
        });

        $response = $this->postJson("/api/spp/bills/{$bill->id}/payments/initiate");

        $response->assertCreated()
            ->assertJsonPath('data.reused_existing', false)
            ->assertJsonPath('data.payment_transaction.attempt_no', 2);

        $this->assertDatabaseCount('payment_transactions', 2);
    }

    public function test_settlement_webhook_marks_payment_and_bill_as_paid_idempotently(): void
    {
        config()->set('midtrans.serverKey', 'server-key-test');

        $bill = $this->createBill();

        $paymentTransaction = PaymentTransaction::create([
            'bill_id' => $bill->id,
            'student_id' => $bill->student_id,
            'provider' => 'midtrans',
            'code' => 'PAY-SETTLE',
            'provider_order_id' => 'SPP-SETTLE',
            'attempt_no' => 1,
            'gross_amount' => $bill->amount,
            'status' => PaymentTransactionStatus::PENDING,
            'initiated_at' => now(),
        ]);

        $payload = [
            'order_id' => $paymentTransaction->provider_order_id,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => (string) $paymentTransaction->gross_amount,
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-transaction-1',
            'status_message' => 'Success, transaction is found',
        ];
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . config('midtrans.serverKey')
        );

        $firstResponse = $this->postJson('/api/payments/midtrans/webhook', $payload);
        $secondResponse = $this->postJson('/api/payments/midtrans/webhook', $payload);

        $firstResponse->assertOk()
            ->assertJsonPath('data.duplicate', false)
            ->assertJsonPath('data.payment_transaction.status', PaymentTransactionStatus::SETTLEMENT);

        $secondResponse->assertOk()
            ->assertJsonPath('data.duplicate', true);

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $paymentTransaction->id,
            'status' => PaymentTransactionStatus::SETTLEMENT,
            'provider_transaction_id' => 'midtrans-transaction-1',
        ]);

        $this->assertDatabaseHas('bills', [
            'id' => $bill->id,
            'status' => BillStatus::PAID,
        ]);

        $this->assertDatabaseCount('payment_webhook_logs', 1);
    }

    private function createBill(): Bill
    {
        $student = Student::create([
            'student_number' => 'STD-' . fake()->unique()->numerify('#####'),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone_number' => '08123456789',
        ]);

        return Bill::create([
            'student_id' => $student->id,
            'bill_number' => 'BILL-' . fake()->unique()->numerify('#####'),
            'title' => 'SPP April 2026',
            'description' => 'Monthly tuition fee',
            'amount' => 500000,
            'due_date' => now()->addWeek()->toDateString(),
            'status' => BillStatus::UNPAID,
        ]);
    }
}
