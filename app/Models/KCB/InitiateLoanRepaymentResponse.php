<?php

namespace App\Models\KCB;

use App\Models\KCB\MoneyDetailsType;

class InitiateLoanRepaymentResponse
{
    public $providertransactionid;
    public $scheduledtransactionid;
    public $newbalance;
    public $paymenttoken;
    public $status;
    public $message;

    public function __construct($providerTransactionId = null, $scheduledTransactionId = null, $newBalance = null, $paymentToken = null, $status = 'PENDING', $message = '')
    {
        $this->providertransactionid = $providerTransactionId;
        $this->scheduledtransactionid = $scheduledTransactionId;
        $this->newbalance = $newBalance;
        $this->paymenttoken = $paymentToken;
        $this->status = $status;
        $this->message = $message;
    }

    public function toXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><initiateloanrepaymentresponse></initiateloanrepaymentresponse>');

        $xml->addChild('providertransactionid', $this->providertransactionid ?? '');
        $xml->addChild('scheduledtransactionid', $this->scheduledtransactionid ?? '');

        // Add new balance if available
        if ($this->newbalance instanceof MoneyDetailsType) {
            $newBalance = $xml->addChild('newbalance');
            $newBalance->addChild('amount', $this->newbalance->amount);
            $newBalance->addChild('currency', $this->newbalance->currency);
        }

        $xml->addChild('paymenttoken', $this->paymenttoken ?? '');
        $xml->addChild('status', $this->status);
        $xml->addChild('message', $this->message);

        return $xml->asXML();
    }
}
