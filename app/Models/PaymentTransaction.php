<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'student_id',
        'provider',
        'code',
        'provider_order_id',
        'provider_transaction_id',
        'attempt_no',
        'gross_amount',
        'status',
        'provider_status',
        'payment_type',
        'fraud_status',
        'status_message',
        'snap_token',
        'snap_redirect_url',
        'metadata',
        'initiated_at',
        'paid_at',
        'expired_at',
        'last_webhook_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'initiated_at' => 'datetime',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'last_webhook_at' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function webhookLogs()
    {
        return $this->hasMany(PaymentWebhookLog::class);
    }
}
