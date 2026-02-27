<?php

namespace App\Enums;

enum CreditPaymentFrequency
{
  case Monthly;
  case Daily;

  public static function valueList(): array
  {
      return collect(self::cases())
          ->map(function ($enum) {
              return $enum->name;
          })->toArray();
  }
}
