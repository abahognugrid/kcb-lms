<?php

namespace App\Exceptions;

class JournalEntrySaveFailedException extends InternalAccountingException
{
    public function __construct(string $message, array $payload)
    {
        parent::__construct($message, $payload);
    }
}
