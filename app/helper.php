<?php

use App\Models\Loan;
if (! function_exists('formatDecisionRanges')) {

    function formatDecisionRanges($minimum, $maximum) {
        if ($minimum==$maximum) {
            return $minimum;
        } elseif($minimum==-999999999999999) {
            return $maximum.' and below';
        } elseif($maximum==999999999999999) {
            return $minimum.' and above';
        } else {
            return $minimum.' to '.$maximum;
        }
    }
}
if (! function_exists('formatInfinityFigures')) {
    function formatInfinityFigures($value=null, $old_value=null)
    {
        if ($value==999999999999999 || $old_value==999999999999999 || $value==-999999999999999 || $old_value==-999999999999999) {
            return '';
        }

        return $value;
    }
}
if (! function_exists('convertAccountStatusCodeToText')) {
    function convertAccountStatusCodeToText($value)
    {
        return Loan::SUPPORTED_Credit_Account_Statuses[$value];
    }
}
if (! function_exists('local')) {
    function local()
    {
        return env('APP_ENV') == 'local' ? true : false;
    }
}
if (! function_exists('staging')) {
    function staging() {
        return env('APP_ENV') == 'staging' ? true : false;
    }
}
if (! function_exists('testing')) {
    function testing() {
        return env('APP_ENV') == 'testing' ? true : false;
    }
}
if (! function_exists('production')) {
    function production() {
        return env('APP_ENV') == 'production' ? true : false;
    }
}
if (! function_exists('percentage')) {
    function percentage($topAmount, $bottomAmount, $decimals = 2): float|int
    {
        if ($bottomAmount == 0) {
            return 0;
        }

        return round($topAmount / $bottomAmount * 100, $decimals);
    }
}

if (! function_exists('validate_date')) {
    function validate_date(string $date, string $format = 'Ymd'): bool
    {
        try {
            $d = DateTime::createFromFormat($format, $date);

            return $d && $d->format($format) === $date;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (! function_exists('xml_to_array')) {
    function xml_to_array(string $xml) : array {
        if (empty($xml)) {
            return [];
        }

        // Implement XML to array conversion
        // This is a simplified example
        return json_decode(json_encode(simplexml_load_string($xml)), true);
    }
}
