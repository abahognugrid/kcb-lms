<?php

namespace App\Exceptions;

use Exception;

class CreditLimitException extends Exception
{
    public const INVALID_PHONE_NUMBER = 'Invalid phone number provided';
    public const API_REQUEST_FAILED = 'Credit limit API request failed';
    public const INVALID_RESPONSE = 'Invalid response from credit limit API';
    public const CONFIGURATION_ERROR = 'Credit limit service configuration error';
    public const CONNECTION_ERROR = 'Failed to connect to credit limit API';
}
