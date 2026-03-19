<?php

namespace App\Actions\Loans;

use App\Enums\LoanApplicationStatus;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\LoanProductType;
use App\Models\LoanSchedule;
use App\Models\Transaction;
use App\Notifications\SmsNotification;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateApprovedLoanAction
{
    public function execute(Transaction $transaction): bool
    {
        try {
            $transaction->loadMissing(['customer', 'loanApplication']);
            $creditAccountType = LoanProductType::first();
            $loanApplicationSession = $transaction->loanApplication->loan_session;
            $loanApplicationSession->loadMissing('loanProductTerm');
            $loanProductTerm = $loanApplicationSession->loanProductTerm;

            DB::beginTransaction();
            $loan = Loan::query()->create([
                'partner_id' => $transaction->partner_id,
                'Customer_ID' => $transaction->customer->id,
                'Loan_Product_ID' => $transaction->loanApplication->Loan_Product_ID,
                'Loan_Application_ID' => $transaction->Loan_Application_ID,
                'Credit_Application_Status' => LoanApplicationStatus::Approved->name, // You can dynamically adjust this based on approval logic
                'Credit_Account_Reference' => Loan::generateReference(),
                'Credit_Account_Date' => $transaction->loanApplication->Credit_Application_Date,
                'Credit_Amount' => $transaction->loanApplication->Amount,
                'Facility_Amount_Granted' => $transaction->loanApplication->Amount,
                'Credit_Amount_Drawdown' => '0.00', // Confirm this @Najja
                'Credit_Account_Type' => $creditAccountType->Code,
                'Currency' => 'UGX',
                'Maturity_Date' => $loanApplicationSession->Maturity_Date,
                'Annual_Interest_Rate_at_Disbursement' => $loanProductTerm->Interest_Rate,
                'Date_of_First_Payment' => $loanApplicationSession->Date_of_First_Payment,
                'Credit_Amortization_Type' => 1, // Refer to the DSM APPENDIX 1.11
                'Credit_Payment_Frequency' => $loanApplicationSession->Credit_Payment_Frequency ?? "Monthly",
                'Number_of_Payments' => $loanApplicationSession->Number_of_Payments ?? 1,
                'Client_Advice_Notice_Flag' => 'Yes',
                'Term' => $loanProductTerm->Value,
                'Type_of_Interest' => 1, // Refer to the DSM APPENDIX 1.9 0-Fixed, 1-Floating
                'Client_Consent_Flag' => 'Yes',
                'Interest_Rate' => $loanProductTerm->Interest_Rate,
                'Interest_Calculation_Method' => $loanProductTerm->Interest_Calculation_Method,
                'Loan_Term_ID' => $loanProductTerm->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $loanApplicationSession->update([
                'Loan_ID' => $loan->id
            ]);

            $transaction->update([
                'Loan_ID' => $loan->id
            ]);

            $application = $loan->loan_application;
            $application->update(['Credit_Application_Status' => 'Approved']);
            Loan::createLoanFees($loan);
            LoanSchedule::generateSchedule($loan);

            $disbursement = LoanDisbursement::createDisbursement($loan);
            $disbursement->saveJournalEntries($transaction->id);

            DB::commit();

            $interestRateMessagePart = $this->getInterestRateMessage($loan);
            $productName = $loan->loan_product->Name;
            $customer = $loan->customer;
            $message = 'Congratulations ' . $customer->First_Name . ', your ' . $productName . ' request of UGX ' .
                number_format($application->Credit_Amount_Approved) . $interestRateMessagePart . ' Dial ' .
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
            // Disburse money to phone
            LoanService::initiateDisbursement(
                $transaction
            );

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getFile());
            Log::error($e->getLine());

            return false;
        }
    }

    protected function getInterestRateMessage(Loan $loan): string
    {
        $message = ' at ';
        $interestRate = $loan->Interest_Rate;
        return $message . $interestRate . '% interest has been approved.';
    }
}
