<?php

namespace App\Models\KCB;

class MoneyDetailsType
{
    public $amount;
    public $currency;

    public function __construct($amount = '0.00', $currency = 'UGX')
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }
}
