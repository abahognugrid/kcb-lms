<?php

namespace App\Jobs\Payments;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ProcessAirtelCallback implements ShouldQueue
{
    use Queueable;

    const AMBIGUOUS_TRANSACTION = 'DP00800001000';
    const INCORRECT_PIN_CODE = 'DP00800001002';

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $details)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * Sample responses from Airtel Callback endpoint
         *
         * Insufficient Funds
         * {"PartnerID":"FSP016","Name":null,"Message":"Reached the LMS successfully","data":{"transaction":{"status_code":"TF","code":"DP00800001007","airtel_money_id":"123589350433","id":"3cf6528c-33b6-4e9c-92f2-6df6f218f297","message":"FAILED. TID 123589350433 FAILED. Insufficient funds."}}}
         *
         * Incorrect PIN
         * {"PartnerID":"FSP016","Name":null,"Message":"Reached the LMS successfully","data":{"transaction":{"status_code":"TF","code":"DP00800001002","airtel_money_id":"123589016349","id":"01d0b5fb-dbda-4e47-a440-e9c49bdf2c8a","message":"WRONG PIN entered, please try again. 2 more wrong attempts will lock your account. To RESET your PIN, go to Self Help, then My PIN and Reset\/Forgot PIN"}}}
         */

        $transaction = Transaction::query()->firstWhere('TXN_ID', data_get($this->details, 'data.transaction.id'));

        if (empty($transaction)) {
            Log::alert('Transaction not found: ' . data_get($this->details, 'data.transaction.id'));

            return;
        }

        if ($this->getTransactionStatus() === TransactionStatus::FAILED->value) {
            // This transaction has already been marked as Failed and
            $transaction->update([
                'Status' => 'Failed',
                'Payment_Reference' => data_get($this->details, 'data.transaction.airtel_money_id'),
                'Narration' => data_get($this->details, 'data.transaction.message'),
            ]);
        }
    }

    public function getTransactionStatus(): string
    {
        $transactionStatus = data_get($this->details, 'data.transaction.status_code');
        $transactionStatusCode = data_get($this->details, 'data.status.code');

        if ($transactionStatus === 'TS' || $transactionStatusCode === 'DP00800001001') {
            return TransactionStatus::SUCCEEDED->value;
        }

        /**
         * The transaction is any of:
         * Ambiguous: The transaction is still processing and is in ambiguous state. Please do the transaction enquiry to fetch the transaction status.
         * In Process: Transaction in pending state. Please check after sometime.
         * Timed out: The transaction was timed out, can be failed or successful. Please check after sometime.
         */
        if (in_array($transactionStatusCode, ['DP00800001000', 'DP00800001006', 'DP00800001024'])) {
            return TransactionStatus::PENDING->value;
        }

        return TransactionStatus::FAILED->value;
    }
}
