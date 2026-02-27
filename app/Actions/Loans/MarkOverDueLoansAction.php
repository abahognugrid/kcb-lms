<?php

namespace App\Actions\Loans;

use App\Enums\CreditPaymentFrequency;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Builder;

// Mark loans as overdue if they are past their maturity date
class MarkOverDueLoansAction
{
    public function execute(): void
    {
        $query = Loan::query()
            ->whereBeforeToday('Maturity_Date')
            ->where('Credit_Account_Status', Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS);

        $query->update([
            'Credit_Account_Status' => Loan::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS,
            'Last_Status_Change_Date' => now()->format('Y-m-d'),
        ]);
    }
}
