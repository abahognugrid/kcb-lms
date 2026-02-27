<?php

namespace App\Enums;

enum PaymentFrequency: int
{
    case Annually = 0;
    case Daily = 2;
    case Monthly = 5;
    case Other = 6;

    public static function getValueFromName($value): ?int
    {
        return match ($value) {
            'Annually' => self::Annually->value,
            'Daily' => self::Daily->value,
            'Monthly' => self::Monthly->value,
            'Other' => self::Other->value,
            default => null
        };
    }
}
