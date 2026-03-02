<?php
// app/Services/LoanRepaymentService.php

namespace App\Services\KCB;

use App\Models\Customer;
use App\Models\KCB\InitiateLoanRepaymentRequest;
use App\Models\KCB\InitiateLoanRepaymentResponse;
use App\Models\KCB\MoneyDetailsType;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\Partner;
use App\Services\LoanService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoanRepaymentService
{
    public function initiateLoanRepayment(InitiateLoanRepaymentRequest $request): InitiateLoanRepaymentResponse
    {
        DB::beginTransaction();

        try {
            // Validate customer exists
            $customer = Customer::where('Telephone_Number', $request->accountholderid)->first();

            if (!$customer) {
                return new InitiateLoanRepaymentResponse(
                    null,
                    null,
                    null,
                    null,
                    'FAILED',
                    'Customer not found'
                );
            }

            // Validate phone number matches customer
            if ($customer->Telephone_Number !== $request->receivingfri) {
                return new InitiateLoanRepaymentResponse(
                    null,
                    null,
                    null,
                    null,
                    'FAILED',
                    'Phone number does not match customer record'
                );
            }

            $loanProduct = LoanProduct::where('Code', $request->productid)->first();
            if (!$loanProduct) {
                return new InitiateLoanRepaymentResponse(
                    null,
                    null,
                    null,
                    null,
                    'FAILED',
                    'Invalid loan product: ' . $request->productid
                );
            }

            $partner = Partner::where('Identification_Code', 'CB011')->first();

            // Find active loans for the customer
            $activeLoan = Loan::where('Customer_ID', $customer->id)->where('partner_id', $partner->id)
                ->whereNot('Credit_Account_Status', Loan::ACCOUNT_STATUS_FULLY_PAID_OFF) // Not Fully Paid
                ->first();

            if (!$activeLoan) {
                return new InitiateLoanRepaymentResponse(
                    null,
                    null,
                    null,
                    null,
                    'FAILED',
                    'No active loan found for customer'
                );
            }


            // Validate repayment amount
            if ($request->amount <= 0) {
                return new InitiateLoanRepaymentResponse(
                    null,
                    null,
                    null,
                    null,
                    'FAILED',
                    'Repayment amount must be greater than zero'
                );
            }

            $totalOutstanding = round($activeLoan->totalOutstandingBalanceExcludingWriteOffs());

            if ($request->amount > $totalOutstanding) {
                return new InitiateLoanRepaymentResponse(
                    null,
                    null,
                    null,
                    null,
                    'FAILED',
                    sprintf(
                        'Repayment amount exceeds outstanding balance. Maximum: %s %s',
                        'UGX ',
                        number_format($totalOutstanding, 2)
                    )
                );
            }

            // Generate unique IDs
            $providerTransactionId = $this->generateProviderTransactionId();
            $scheduledTransactionId = $this->generateScheduledTransactionId();
            $paymentToken = $this->generatePaymentToken();

            LoanService::initiateRepayment($partner, $customer, $request->amount, $activeLoan);

            DB::commit();
            return new InitiateLoanRepaymentResponse(
                $providerTransactionId,
                $scheduledTransactionId,
                null,
                $paymentToken,
                'SUCCESSFUL',
                'Loan repayment initiated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Loan repayment initiation failed: ' . $e->getMessage(), [
                'request_reference' => $request->requestreference,
                'customer_id' => $request->accountholderid,
                'error' => $e->getTraceAsString()
            ]);

            return new InitiateLoanRepaymentResponse(
                null,
                null,
                null,
                null,
                'FAILED',
                'Loan repayment failed: ' . $e->getMessage()
            );
        }
    }

    private function generateProviderTransactionId(): string
    {
        return 'PTX' . date('YmdHis') . rand(1000, 9999);
    }

    private function generateScheduledTransactionId(): string
    {
        return 'STX' . date('YmdHis') . rand(1000, 9999);
    }

    private function generatePaymentToken(): string
    {
        return Str::upper(Str::random(16));
    }
}
