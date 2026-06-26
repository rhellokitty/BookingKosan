<?php

namespace App\Support;

final class BillStatus
{
    public const UNPAID = 'unpaid';
    public const PAID = 'paid';

    public static function values(): array
    {
        return [
            self::UNPAID,
            self::PAID,
        ];
    }
}
