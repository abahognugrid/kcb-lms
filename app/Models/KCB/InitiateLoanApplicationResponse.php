<?php

namespace App\Models\KCB;

use App\Models\KCB\LoanAccount;

class InitiateLoanApplicationResponse
{
    public $loanaccount;
    public $status;
    public $message;

    public function __construct($loanaccount = null, $status = 'SUCCESSFUL', $message = '')
    {
        $this->loanaccount = $loanaccount;
        $this->status = $status;
        $this->message = $message;
    }

    public function toXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><initiateloanapplicationresponse></initiateloanapplicationresponse>');

        // Add loan account details if available
        if ($this->loanaccount instanceof LoanAccount) {
            $loanAccount = $xml->addChild('loanaccount');
            $loanAccount->addChild('accountnumber', $this->loanaccount->accountnumber);
            $loanAccount->addChild('status', $this->loanaccount->status);

            $due = $loanAccount->addChild('due');
            $due->addChild('amount', $this->loanaccount->due->amount);
            $due->addChild('currency', $this->loanaccount->due->currency);

            $loanAccount->addChild('duedate', $this->loanaccount->duedate);
            $loanAccount->addChild('tenor', $this->loanaccount->tenor);
            $loanAccount->addChild('loantype', $this->loanaccount->loantype);
            $loanAccount->addChild('interest', $this->loanaccount->interest);
        }

        $xml->addChild('status', $this->status);
        $xml->addChild('message', $this->message);

        return $xml->asXML();
    }
}
