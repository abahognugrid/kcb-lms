<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneNumber implements ValidationRule
{
    protected $validPrefixes = [
        '25676', '25677', '25678',
        '25639', '25631', '25679',
        '25670', '25674', '25675',
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^\d{12}$/', $value)) {
            $fail('The :attribute must be exactly 12 digits.');
            return;
        }

        if (!str_starts_with($value, '256')) {
            $fail('The :attribute must start with 256.');
            return;
        }

        if (!collect($this->validPrefixes)->contains(fn($prefix) => str_starts_with($value, $prefix))) {
            $fail('The :attribute has an invalid prefix.');
        }
    }
}
