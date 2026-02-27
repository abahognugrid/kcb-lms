<?php

namespace App\Models\KCB;

class CustomerRegistrationResponse
{
    public $customerid;
    public $savingsaccount;
    public $status;
    public $message;

    public function __construct($customerid = null, $savingsaccount = null, $status = null, $message = null)
    {
        $this->customerid = $customerid;
        $this->savingsaccount = $savingsaccount;
        $this->status = $status;
        $this->message = $message;
    }

    public function toXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><customerregistrationresponse></customerregistrationresponse>');

        $xml->addChild('customerid', $this->customerid ?? '');
        // Add savings account details as nested XML
        if ($this->savingsaccount instanceof SavingsAccountResponse) {
            $savingsAccount = $xml->addChild('savingsaccount');
            $savingsAccount->addChild('accountnumber', $this->savingsaccount->accountnumber);
            $savingsAccount->addChild('status', $this->savingsaccount->status);

            // Add balance with amount and currency as children
            $balance = $savingsAccount->addChild('balance');
            $balance->addChild('amount', $this->savingsaccount->amount);
            $balance->addChild('currency', $this->savingsaccount->currency);

            $savingsAccount->addChild('savingsaccounttype', $this->savingsaccount->savingsaccounttype);
        }
        $xml->addChild('status', $this->status ?? '');
        $xml->addChild('message', $this->message ?? '');

        return $xml->asXML();
    }
}
