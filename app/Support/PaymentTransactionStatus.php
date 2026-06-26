<?php

namespace App\Support;

final class PaymentTransactionStatus
{
    public const CREATING = 'creating';
    public const INITIATED = 'initiated';
    public const PENDING = 'pending';
    public const CHALLENGE = 'challenge';
    public const SETTLEMENT = 'settlement';
    public const FAILED = 'failed';
    public const EXPIRED = 'expired';
    public const CANCELLED = 'cancelled';
    public const UNKNOWN = 'unknown';

    public static function reusableStatuses(): array
    {
        return [
            self::CREATING,
            self::INITIATED,
            self::PENDING,
            self::CHALLENGE,
        ];
    }

    public static function terminalStatuses(): array
    {
        return [
            self::SETTLEMENT,
            self::FAILED,
            self::EXPIRED,
            self::CANCELLED,
        ];
    }
}
