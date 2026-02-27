<?php

namespace App\Models\KCB;

class SavingsAccountResponse
{
    public $accountnumber;
    public $status;
    public $amount;
    public $currency;
    public $savingsaccounttype;

    public function __construct($accountNumber, $status = 'ACTIVE', $amount = '0.00', $currency = 'UGX', $savingsAccountType = 'PERSONAL')
    {
        $this->accountnumber = $accountNumber;
        $this->status = $status;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->savingsaccounttype = $savingsAccountType;
    }
}
