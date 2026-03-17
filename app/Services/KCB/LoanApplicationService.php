<?php

namespace App\Services\KCB;

use App\Models\Accounts\Account;
use App\Models\Customer;
use App\Models\KCB\InitiateLoanApplicationRequest;
use App\Models\KCB\InitiateLoanApplicationResponse;
use App\Models\KCB\LoanAccount;
use App\Models\KCB\MoneyDetailsType;
use App\Models\LmsUssdSessionTracking;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\LoanProductTerm;
use App\Models\Partner;
use App\Services\Account\AccountSeederService;
use App\Services\LoanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanApplicationService
{
    public function initiateLoanApplication(InitiateLoanApplicationRequest $request): InitiateLoanApplicationResponse
    {
        DB::beginTransaction();
        try {
            //check if requestreference is already used
            $requestId = $request->requestreference;
            $usedApplicationRequestID = LoanApplication::where('Request_ID', $requestId)->first();
            if ($usedApplicationRequestID) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'Request ID has already been used.'
                );
            }


            // Validate customer exists
            $customer = Customer::where('Telephone_Number', $request->resource)->first();
            $partnerId = Partner::where('Identification_Code', 'CB011')->first()->id;
            if (!$customer) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'Customer not found'
                );
            }

            // Validate loan product exists and is active
            $loanProduct = LoanProduct::where('Code', $request->productid)->where('partner_id', $partnerId)->first();

            if (!$loanProduct) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'Invalid or inactive loan product: ' . $request->productid
                );
            }

            // Validate amount against product limits
            if ($request->amount < $loanProduct->Minimum_Principal_Amount || $request->amount > $loanProduct->Maximum_Principal_Amount) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    sprintf(
                        'Amount must be between %s and %s',
                        number_format($loanProduct->Minimum_Principal_Amount, 2),
                        number_format($loanProduct->Maximum_Principal_Amount, 2)
                    )
                );
            }

            $creditLimit = $customer->creditLimits()->latest()->first();
            if (!$creditLimit) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'You do not have a credit limit assigned yet!'
                );
            }

            $maxAmount = $creditLimit->credit_limit;
            if ($request->amount > $maxAmount) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'The requested amount is greater than your credit limit of ' . number_format($maxAmount)
                );
            }

            $pendingApplication = LoanApplication::where('Customer_ID', $customer->id)->where('Credit_Application_Status', 'Pending')->first();
            if ($pendingApplication) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'You still have a pending application. please wait until it is finalized'
                );
            }

            //check float amount
            $disbursement_ova = Account::where('partner_id', $partnerId)
                ->where('slug', AccountSeederService::DISBURSEMENT_OVA_SLUG)
                ->first();
            if (!$disbursement_ova || $disbursement_ova->current_balance < $request->amount) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'Insufficient float balance for disbursement'
                );
            }

            $loanProductTerm = LoanProductTerm::where('partner_id', $partnerId)->where('Value', $request->tenor)->first();
            if (!$loanProductTerm) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'Loan product term of ' . $request->tenor . ' days not found!'
                );
            }

            // Check if customer has existing pending or active loans
            $existingLoans = Loan::where('Customer_ID', $customer->id)
                ->where('partner_id', $partnerId)
                ->whereIn('Credit_Account_Status', [Loan::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS, Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS]) // Beyond terms / Active
                ->count();

            if ($existingLoans > 0) {
                return new InitiateLoanApplicationResponse(
                    null,
                    'FAILED',
                    'Customer has existing pending or active loans'
                );
            }


            if ($request->due_date) {
                $dateOnly = substr($request->due_date, 0, 9);
                $dueDate = Carbon::parse($dateOnly);
            } else {
                // Calculate due date based on tenor
                $dueDate = Carbon::now()->addDays($request->tenor);
                if (config('lms.loans.enable_ageing')) {
                    $dueDate = now()->subDays(config('lms.loans.back_date_days'));
                }
            }
            $applicationDate = $dueDate->copy()->subDays($request->tenor);

            $amount = round($request->amount);
            $loan_application = LoanApplication::create([
                'Request_ID' => $request->requestreference,
                'partner_id' => $partnerId,
                'Customer_ID' => $customer->id,
                'Loan_Product_ID' => $loanProduct->id,
                'Loan_Purpose' => $request->loantype,
                'Applicant_Classification' => $customer->Classification,
                'Credit_Application_Date' => $applicationDate,
                'Amount' => $amount,
                'Credit_Application_Status' => 'Pending',
                'Credit_Account_or_Loan_Product_Type' => 14,
                'Credit_Application_Duration' => '0', // time between application and the time it is approved or rejected. This is auto so zero(0)
                'Client_Consent_flag' => 'Yes',
                'Country' => $customer->Country,
                'District' => $customer->District,
                'Subcounty' => $customer->Subcounty,
                'Parish' => $customer->Parish,
                'Village' => $customer->Village,
                'Last_Status_Change_Date' => Carbon::now()->toDateString(),
                'Credit_Amount_Approved' => $amount,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $sessionData = [
                "requestId" => $request->requestreference,
                "Customer_Phone_Number" => $customer->Telephone_Number,
                "Loan_Application_ID" => $loan_application->id,
                "Loan_Producd_Code" => $loanProduct->Code,
                "Loan_Producd_Term_Code" => $loanProductTerm->Code,
                "Credit_Payment_Frequency" => 'Once',
                "Number_of_Payments" => 1,
                'Date_of_First_Payment' => $dueDate,
                'Maturity_Date' => $dueDate,
            ];

            LmsUssdSessionTracking::create(array_merge(
                [
                    "requestId" => $request->requestreference,
                    "Customer_Phone_Number" => $customer->Telephone_Number,
                ],
                $sessionData
            ));

            $due = new MoneyDetailsType(
                number_format($amount, 2, '.', ''),
                $request->currency
            );

            $loanAccount = new LoanAccount(
                (string) $loan_application->id,
                $loan_application->Credit_Application_Status, // This will be "PENDING" based on our mapping
                $due,
                $dueDate->format('Y-m-d'),
                (string) $request->tenor,
                $request->loantype,
                number_format($loanProduct->interest_rate, 2, '.', '')
            );

            DB::commit();
            $partner = Partner::find($loan_application->partner_id);
            $customer = Customer::find($loan_application->Customer_ID);

            LoanService::initiateDisbursement(
                $partner,
                $customer,
                $loan_application->Amount,
                $loan_application->id
            );
            return new InitiateLoanApplicationResponse(
                $loanAccount,
                'SUCCESSFUL',
                'Loan application initiated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Loan application initiation failed: ' . $e->getMessage(), [
                'request_reference' => $request->requestreference,
                'customer_id' => $customer->id,
                'error' => $e->getTraceAsString()
            ]);

            return new InitiateLoanApplicationResponse(
                null,
                'FAILED',
                'Loan application failed: ' . $e->getMessage()
            );
        }
    }
}
