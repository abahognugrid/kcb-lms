<?php

namespace App\Enums;

enum TransactionFailure: string
{
    case InsufficientBalance = 'Insufficient balance';
    case ProcessingError = 'Processing error';
    case RequestError = 'Request error';
}
