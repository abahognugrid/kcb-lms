<?php

namespace App\Models\KCB;

class LoanAccount
{
    public $accountnumber;
    public $status;
    public $due;
    public $duedate;
    public $tenor;
    public $loantype;
    public $interest;

    public function __construct($accountNumber, $status, MoneyDetailsType $due, $dueDate, $tenor, $loanType, $interest)
    {
        $this->accountnumber = $accountNumber;
        $this->status = $status;
        $this->due = $due;
        $this->duedate = $dueDate;
        $this->tenor = $tenor;
        $this->loantype = $loanType;
        $this->interest = $interest;
    }
}
