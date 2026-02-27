<?php

namespace App\Actions\Loans;

use App\Enums\AccountClosureReason;
use App\Enums\LoanAccountType;
use App\Models\Loan;
use App\Models\WrittenOffLoan;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WriteOffLoanAction
{
    /**
     * @throws Exception
     */
    public function execute(Loan $loan, array $details): Loan
    {
        try {
            DB::transaction(function () use ($loan, $details) {
                if (in_array($loan->Credit_Account_Status, [LoanAccountType::WrittenOff->value, LoanAccountType::PaidOff->value])) {
                    throw new Exception('Loan has already been written off or is fully paid.');
                }

                if (now()->isBefore($loan->Maturity_Date) || $loan->schedule->sum('total_outstanding') === 0) {
                    throw new Exception('This loan is not in arrears.');
                }

                $loanLossProvisionLiabilityAccount = $loan->loan_product->lossProvisionAccount();

                if (empty($loanLossProvisionLiabilityAccount)) {
                    throw new Exception('Loan Loss Provision Account not found.');
                }

                $writtenOffAmount = $loan->getOutstandingPrincipal();

                if ($loanLossProvisionLiabilityAccount->current_balance < $writtenOffAmount) {
                    throw new Exception('Insufficient balance on Loan Loss Provision Account.');
                }

                $loan->Credit_Account_Status = LoanAccountType::WrittenOff->value;
                $loan->Last_Status_Change_Date = now()->toDateString();
                $loan->Credit_Account_Closure_Reason = AccountClosureReason::WrittenOff->value;
                $loan->Credit_Account_Closure_Date = $details['write_off_date'];
                $loan->Credit_Account_Closure_Officer = Auth::user()?->id;
                $loan->Written_Off_Amount = $writtenOffAmount;
                $loan->Written_Off_Date = $details['write_off_date'];
                $loan->Written_Off_Reason = AccountClosureReason::WrittenOff->value;
                $loan->Written_Off_Officer = Auth::user()?->id;
                $loan->save();

                // This will help us track status change history on any model
                $loan->setStatus(LoanAccountType::WrittenOff->name, $loan->Written_Off_Reason);

                $loan_wite_off = WrittenOffLoan::createTransactable($loan, $details);
                $loan_wite_off->saveJournalEntries();

                // todo: Is this right, clearing the interest to zero and losing the known amount before write-off. Suggestion: Keep it somewhere perhaps in a column: - interest_written_off?
                // ---- We will use this written off amount to track the balance after a loan has been written off.
                // $loan->schedule()->update(['interest_remaining' => 0]);
                $schedules = $loan->schedule()->get();
                foreach ($schedules as $schedule) {
                    $interestAmount = $schedule->interest_remaining;
                    if ($interestAmount > 0) {
                        $schedule->update(['total_outstanding' => $schedule->total_outstanding - $interestAmount, 'interest_remaining' => 0]);
                    }
                }

                // todo: Update any other related fees to zero.

                // todo: Perform write off.
            });

            return $loan;
        } catch (Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
