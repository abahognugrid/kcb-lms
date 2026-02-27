<?php

namespace App\Enums;

enum LoanApplicationStatus: int
{
    case Approved = 0;
    case Pending = 2;
    case Rejected = 6;

    public static function getValueFromName($value)
    {
        return match ($value) {
            'Approved' => self::Approved->value,
            'Pending' => self::Pending->value,
            'Rejected' => self::Rejected->value,
            default => null
        };
    }
}
