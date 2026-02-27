<?php

namespace App\Enums;

enum LoanAccountType: int
{
    case BeyondTerms = 1;
    case Restructured = 2;
    case WrittenOff = 3;
    case PaidOff = 4;
    case WithinTerms = 5;
    case WrittenOffRecovery = 6;
    case Forfeiture = 7;

    public static function formattedName($value): string
    {
        return str(self::tryFrom($value)->name)->snake()->replace('_', ' ')->title()->toString();
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($enum) {
            return [$enum->value => $enum->name];
        })->toArray();
    }
}
