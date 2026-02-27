<?php

namespace App\Contracts;

interface SmsProviderInterface
{
    public function sendSingle(string $phoneNumber, string $message): array;
    public function sendBulk(array $phoneNumbers, string $message): array;
}
