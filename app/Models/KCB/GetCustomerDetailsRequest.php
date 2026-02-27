<?php

namespace App\Models\KCB;

use Illuminate\Database\Eloquent\Model;

class GetCustomerDetailsRequest extends Model
{
    protected $fillable = [
        'serviceproviderid',
        'productid',
        'resource',
        'requesttype'
    ];

    public static function fromXml($xmlContent)
    {
        $xml = simplexml_load_string($xmlContent);

        if (!$xml) {
            throw new \Exception('Invalid XML format');
        }

        return new self([
            'serviceproviderid' => (string) $xml->serviceproviderid,
            'productid' => (string) $xml->productid,
            'resource' => (string) $xml->resource,
            'requesttype' => (string) $xml->requesttype,
        ]);
    }
}
