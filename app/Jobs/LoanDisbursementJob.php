<?php

namespace App\Jobs;

use App\Mail\FailedDisbursement;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\SmsNotification;
use App\Services\LoanService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoanDisbursementJob implements ShouldQueue
{
    use Queueable;

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
        if ($success) {
            $interestRateMessagePart = $this->getInterestRateMessage($loan);
            $productName = $loan->loan_product->Name;
            $customer = $loan->customer;
            $message = 'Congratulations ' . $customer->First_Name . ', your ' . $productName . ' request of UGX ' .
                number_format($loan->application->Credit_Amount_Approved) . $interestRateMessagePart . ' Dial ' .
                $loan->loan_product->ussdCode() . ' to repay by ' .
                $loan->Maturity_Date->toDateString() . ' to avoid late fees.';

            $customer->notify(
                new SmsNotification(
                    $message,
                    $customer->Telephone_Number,
                    $customer->id,
                    $loan->partner_id,
                    $loan->partner->smsPrice(),
                    $loan->partner->smsCost(),
                )
            );
        } else {
            Log::warning("Disbursement failed for transaction {$transaction->id}");
            $admins = User::where('is_admin', true)->get();
            Mail::to($admins)->queue(new FailedDisbursement($loan));
        }
    }

    protected function getInterestRateMessage(Loan $loan): string
    {
        $message = ' at ';
        $interestRate = $loan->Interest_Rate;
        return $message . $interestRate . '% interest has been approved.';
    }
}
