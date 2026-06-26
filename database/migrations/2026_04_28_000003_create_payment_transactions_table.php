<?php

use App\Support\PaymentTransactionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('midtrans');
            $table->string('code')->unique();
            $table->string('provider_order_id')->unique();
            $table->string('provider_transaction_id')->nullable()->index();
            $table->unsignedInteger('attempt_no');
            $table->unsignedBigInteger('gross_amount');
            $table->string('status')->default(PaymentTransactionStatus::CREATING);
            $table->string('provider_status')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('fraud_status')->nullable();
            $table->string('status_message')->nullable();
            $table->string('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('last_webhook_at')->nullable();
            $table->timestamps();

            $table->unique(['bill_id', 'attempt_no']);
            $table->index(['bill_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
