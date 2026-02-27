<?php
// app/Models/CustomerRegistrationRequest.php

namespace App\Models\KCB;

use Illuminate\Database\Eloquent\Model;

class CustomerRegistrationRequest extends Model
{
    protected $fillable = [
        'requestreference',
        'serviceproviderid',
        'productid',
        'resource',
        'accountholderid',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'dob',
        'idtype',
        'idnumber',
        'language'
    ];

    public static function fromXml($xmlContent)
    {
        $xml = simplexml_load_string($xmlContent);

        return new self([
            'requestreference' => (string) $xml->requestreference,
            'serviceproviderid' => (string) $xml->serviceproviderid,
            'productid' => (string) $xml->productid,
            'resource' => (string) $xml->resource,
            'accountholderid' => (string) $xml->accountholderid,
            'firstname' => (string) $xml->firstname,
            'middlename' => (string) $xml->middlename,
            'lastname' => (string) $xml->lastname,
            'gender' => (string) $xml->gender,
            'dob' => (string) $xml->dob,
            'idtype' => (string) $xml->idtype,
            'idnumber' => (string) $xml->idnumber,
            'language' => (string) $xml->language,
        ]);
    }
}
