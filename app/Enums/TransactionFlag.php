<?php

namespace App\Enums;

enum TransactionFlag: int
{
    case Normal = 0;
    case CompletedProcessing = 1;
    case PendingProcessing = 2;
    case FailedProcessing = 3;

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
