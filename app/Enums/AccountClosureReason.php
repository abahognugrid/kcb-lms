<?php

namespace App\Enums;

enum AccountClosureReason: int
{
    case Restructured = 0;
    case SettledForeclosure = 1;
    case SettledNormal = 2;
    case WrittenOff = 3;
    case SettledEarly = 4;
    case CoolingOff = 5;

    public static function getValueFromName($value): ?int
    {
        return match ($value) {
            'Restructured' => self::Restructured->value,
            'SettledForeclosure' => self::SettledForeclosure->value,
            'SettledNormal' => self::SettledNormal->value,
            'WrittenOff' => self::WrittenOff->value,
            'SettledEarly' => self::SettledEarly->value,
            'CoolingOff' => self::CoolingOff->value,
            default => null
        };
    }
}
