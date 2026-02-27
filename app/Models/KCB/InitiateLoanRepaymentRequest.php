<?php

namespace App\Models\KCB;

use Illuminate\Database\Eloquent\Model;

class InitiateLoanRepaymentRequest extends Model
{
    protected $fillable = [
        'requestreference',
        'accountholderid',
        'receivingfri',
        'amount',
        'currency',
        'serviceproviderid',
        'productid',
        'callbackurl'
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
            'accountholderid' => (string) $xml->accountholderid,
            'receivingfri' => (string) $xml->receivingfri,
            'amount' => $amount,
            'currency' => $currency,
            'serviceproviderid' => (string) $xml->serviceproviderid,
            'productid' => (string) $xml->productid,
            'callbackurl' => (string) ($xml->callbackurl ?? ''),
        ]);
    }
}
