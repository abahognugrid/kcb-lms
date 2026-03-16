<?php

namespace App\Models\KCB;

use Illuminate\Database\Eloquent\Model;

class InitiateLoanApplicationRequest extends Model
{
    protected $fillable = [
        'requestreference',
        'resource',
        'accountholderid',
        'amount',
        'currency',
        'tenor',
        'loantype',
        'callbackurl',
        'serviceproviderid',
        'productid',
        'message',
        'due_date'
    ];

    public static function fromXml($xmlContent)
    {
        $xml = simplexml_load_string($xmlContent);

        if (!$xml) {
            throw new \Exception('Invalid XML format');
        }
        // Extract amount and currency from moneydetailstype
        $amount = (string) $xml->amount->amount ?? '0.00';
        $currency = (string) $xml->amount->currency ?? 'UGX';

        return new self([
            'requestreference' => (string) $xml->requestreference,
            'resource' => (string) $xml->resource,
            'accountholderid' => (string) $xml->accountholderId,
            'amount' => $amount,
            'currency' => $currency,
            'tenor' => (int) $xml->tenor,
            'loantype' => (string) $xml->loantype,
            'callbackurl' => (string) ($xml->callbackurl ?? ''),
            'serviceproviderid' => (string) $xml->serviceproviderid,
            'productid' => (string) $xml->productid,
            'message' => (string) ($xml->message ?? ''),
        ]);
    }
}
