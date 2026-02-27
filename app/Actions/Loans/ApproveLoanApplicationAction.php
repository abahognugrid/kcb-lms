<?php

namespace App\Actions\Loans;

use App\Events\LoanApplicationApproved;
use App\Exceptions\InsufficientAccountBalanceException;
use App\Models\Accounts\Account;
use App\Models\LoanApplication;
use App\Services\Account\AccountSeederService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ApproveLoanApplicationAction
{
    /**
     * @throws InsufficientAccountBalanceException
     */
    public function execute(LoanApplication $loanApplication, array $details): void
    {
        /**
         * Updating both the loan application and the down payment transaction
         * We shall be switching to storing approvals on the loan application and eventually deprecate the down payment transaction approval.
         *
         * @todo: Refactor to store approvals on the loan application only
         */
        $disbursement_account = Account::where('partner_id', $loanApplication->partner_id)
            ->where('slug', AccountSeederService::DISBURSEMENT_OVA_SLUG)
            ->first();

        if ($disbursement_account->balance < $loanApplication->Amount) {
            throw new InsufficientAccountBalanceException('Insufficient balance in the disbursement account', []);
        }

        $loanApplication->update([
            'Credit_Application_Status' => 'Approved',
            'Approved_By' => Arr::get($details, 'Approved_By'),
            'Approval_Date' => now()->toDateTimeString(),
            'Approval_Reference' => Arr::get($details, 'Approval_Reference'),
            'Approval_Narration' => Arr::get($details, 'Approval_Narration'),
        ]);

        event(new LoanApplicationApproved($loanApplication));
    }
}
