<?php

namespace App\Enums;

enum MaritalStatus: int
{
    case Single = 0;
    case Divorced = 1;
    case Married = 2;
    case Separated = 3;
    case Widowed = 4;
    case Annulled = 6;
    case Cohabitating = 7;
    case Other = 8;

    public static function getValueFromName($value): ?int
    {
        return match ($value) {
            'Single' => self::Single->value,
            'Divorced' => self::Divorced->value,
            'Married' => self::Married->value,
            'Separated' => self::Separated->value,
            'Widowed' => self::Widowed->value,
            'Annulled' => self::Annulled->value,
            'Cohabitating' => self::Cohabitating->value,
            'Other' => self::Other->value,
            default => null
        };
    }
}
