<?php

namespace App\Jobs;

use App\Models\CreditLimit;
use App\Models\Transaction;
use App\Notifications\SmsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendRepaymentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $transactionIds;

    /**
     * Create a new job instance.
     */
    public function __construct(array $transactionIds)
    {
        $this->transactionIds = $transactionIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transactions = Transaction::whereIn('id', $this->transactionIds)
            ->with(['loan.loan_product.switch', 'customer', 'partner'])
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        // Call the existing notification method from your command
        $this->sendPaymentNotification($transactions);
    }

    protected function sendPaymentNotification($transactions): void
    {
        $totalPaid = $transactions->sum('Amount');
        /** @var Transaction $firstTransaction **/
        $firstTransaction = $transactions->first();
        $loanProductName = $this->getProductNameForSms($firstTransaction);
        $ussdCode = $this->getUssdCode($firstTransaction);
        $customer = $firstTransaction->customer;
        $loans = $customer->loans()
            ->whereNot('Credit_Account_Status', 4)
            ->latest()
            ->get();

        // Calculate total outstanding balance (only positive amounts)
        $totalOutstanding = round($loans->sum(fn($loan) => $loan->totalOutstandingBalance()));

        // Build the notification message
        $message = "You paid UGX " . number_format($totalPaid) . " to $loanProductName";

        if ($totalOutstanding == 0) {
            $message .= '. Your loan is now fully paid. Dial ' . $ussdCode . ' to get a new loan.';
        } else {
            $message .= ". Outstanding balance: UGX " . number_format($totalOutstanding) .
                ". Dial " . $ussdCode . " to pay now.";
        }

        // Send the notification
        $firstTransaction->customer->notify(new SmsNotification(
            $message,
            $firstTransaction->customer->Telephone_Number,
            $firstTransaction->customer->id,
            $firstTransaction->partner_id,
            $firstTransaction->partner->smsPrice(),
            $firstTransaction->partner->smsCost(),
        ));

        // Update credit limits if needed
        if ($firstTransaction->loan->product->Allows_Multiple_Loans && $totalOutstanding == 0) {
            // $this->updateCreditLimits($transactions);
            CreditLimit::where('customer_id', $customer->id)->where('partner_id', $firstTransaction->partner_id)->delete();
        }
    }

    private function updateCreditLimits($transactions): void
    {
        $firstTransaction = $transactions->first();
        $totalAmount = $transactions->sum(function ($t) {
            return $t->Amount;
        });

        $creditLimit = CreditLimit::firstOrNew([
            'customer_id' => $firstTransaction->customer->id,
            'partner_id' => $firstTransaction->partner->id
        ]);

        // Calculate new values properly
        $newUsedCredit = max($creditLimit->used_credit - $totalAmount, 0);
        $newAvailableCredit = $creditLimit->available_credit + $totalAmount;

        $creditLimit->update([
            'used_credit' => $newUsedCredit,
            'available_credit' => $newAvailableCredit,
            'updated_at' => Carbon::now()
        ]);
        $this->sendCreditLimitNotification($transactions, $creditLimit);
    }

    private function getProductNameForSms(Transaction $transaction)
    {
        $loanProduct = $transaction->loan->loan_product;
        return $loanProduct->Name;
    }

    private function sendCreditLimitNotification($transactions, $creditLimit): void
    {
        $firstTransaction = $transactions->first();
        $customer = $firstTransaction->customer;
        $partner = $firstTransaction->partner;

        // Get the earliest maturity date from all loans
        $maturityDate = $transactions->min(function ($t) {
            return $t->loan->Maturity_Date;
        });

        // Format the date nicely (e.g., "15th September 2023")
        $formattedDate = $maturityDate ? $maturityDate->format('jS F Y') : 'soon';

        $availableCredit = $creditLimit->available_credit ?? 0;

        if ($availableCredit > 0) {
            $ussdCode = $this->getUssdCode($firstTransaction);
            $institutionName = $partner->Institution_Name;

            $message = "Dear {$customer->name}, you have UGX " . number_format($availableCredit) .
                " available credit with $institutionName. Borrow this amount before " .
                "$formattedDate. Dial $ussdCode to get your loan now.";

            $customer->notify(new SmsNotification(
                $message,
                $customer->Telephone_Number,
                $customer->id,
                $partner->id,
                $partner->smsPrice(),
                $partner->smsCost(),
            ));
        }
    }

    /**
     * @param Transaction $firstTransaction
     * @return string
     */
    public function getUssdCode(Transaction $firstTransaction): string
    {
        if (empty($firstTransaction->Loan_Application_ID) && empty($firstTransaction->Loan_ID)) {
            return config('lms.ussd_code');
        }

        // A transaction attached to a loan application but without a loan e.g. down payment
        if (empty($firstTransaction->Loan_ID) && $firstTransaction->Loan_Application_ID) {
            $firstTransaction->loadMissing('loanApplication.loan_product');

            return $firstTransaction->loanApplication->loan_product->ussdCode();
        }

        // This transaction is attached to a loan.
        return $firstTransaction->loan->loan_product->ussdCode();
    }
}
