<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case SUCCEEDED = 'SUCCEEDED';
    case FAILED = 'FAILED';
    case PENDING = 'PENDING';
}
