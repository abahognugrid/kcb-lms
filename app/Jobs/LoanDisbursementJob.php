<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\LoanService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class LoanDisbursementJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle()
    {
        $transaction = $this->transaction->fresh('loan');
        if (!$transaction || !$transaction->loan) {
            Log::warning("Transaction or associated loan not found for transaction ID: {$this->transaction->id}");
            return;
        }
        $loan = $transaction->loan;
        $loan->update(['Disbursement_Status' => 'Processing']);

        $success = LoanService::initiateDisbursement($transaction);

        $loan->update([
            'Disbursement_Status' => $success ? 'Completed' : 'Failed'
        ]);

        if (!$success) {
            Log::warning("Disbursement failed for transaction {$transaction->id}");
        }
    }
}
