<?php

namespace App\Services\Contracts;


interface ProvidesTransactableAPIs
{
    public function collect(string $phone_number, int|float $amount, string $txn_reference, string $reason = "Deposit"): array;
    public function disburse(string $phone_number, int|float $amount, string $txn_reference, string $reason = "Withdraw"): array;
    public function collectionStatus(string $collection_txn_id): array;
    public function disbursementStatus(string $txn_id): array;
}
