<?php

namespace App\Exceptions;

use Exception;
/**
 * ExpectedException are for client induces error. Say trying to 
 * access what they don't have permission to.
 * 
 * Another example would be validations of submitted forms 
 * 
 * Defualt for this set to 400 bad reequest for easy of use in controllers.
 * 
 * And many more.
 */
class ExpectedException extends Exception
{

    const BAD_REQUEST = 400;
    public function __construct($message, $code = null)
    {
        parent::__construct($message, $code ??self::BAD_REQUEST);
    }
}
