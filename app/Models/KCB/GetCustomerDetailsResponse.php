<?php
// app/Models/GetCustomerDetailsResponse.php

namespace App\Models\KCB;

class GetCustomerDetailsResponse
{
    public $customerid;
    public $registrationstatus;
    public $creditlimit;
    public $status;
    public $message;
    public $loanaccounts;

    public function __construct($customerid = null, $registrationstatus = 'UNREGISTERED', $creditlimit = null, $status = 'SUCCESSFUL', $message = '', $loanaccounts = [])
    {
        $this->customerid = $customerid;
        $this->registrationstatus = $registrationstatus;
        $this->creditlimit = $creditlimit;
        $this->status = $status;
        $this->message = $message;
        $this->loanaccounts = $loanaccounts;
    }

    public function toXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><getcustomerdetailsresponse></getcustomerdetailsresponse>');

        $xml->addChild('customerid', $this->customerid ?? '');
        $xml->addChild('registrationstatus', $this->registrationstatus);

        // Add credit limit if available
        if ($this->creditlimit instanceof MoneyDetailsType) {
            $creditLimit = $xml->addChild('creditlimit');
            $creditLimit->addChild('amount', $this->creditlimit->amount);
            $creditLimit->addChild('currency', $this->creditlimit->currency);
        }

        $xml->addChild('status', $this->status);
        $xml->addChild('message', $this->message);

        // Add loan accounts if available
        if (!empty($this->loanaccounts)) {
            $loanAccounts = $xml->addChild('loanaccounts');
            foreach ($this->loanaccounts as $loanAccount) {
                $account = $loanAccounts->addChild('loanaccount');
                $account->addChild('accountnumber', $loanAccount->accountnumber);
                $account->addChild('status', $loanAccount->status);

                $due = $account->addChild('due');
                $due->addChild('amount', $loanAccount->due->amount);
                $due->addChild('currency', $loanAccount->due->currency);

                $account->addChild('duedate', $loanAccount->duedate);
                $account->addChild('tenor', $loanAccount->tenor);
                $account->addChild('loantype', $loanAccount->loantype);
                $account->addChild('interest', $loanAccount->interest);
            }
        }

        return $xml->asXML();
    }
}
