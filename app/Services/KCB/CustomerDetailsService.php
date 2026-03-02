<?php
// app/Services/CustomerDetailsService.php

namespace App\Services\KCB;

use App\Models\CreditLimit;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\KCB\GetCustomerDetailsRequest;
use App\Models\KCB\GetCustomerDetailsResponse;
use App\Models\KCB\MoneyDetailsType;
use App\Models\KCB\LoanAccount;
use App\Models\LoanProduct;
use App\Models\Partner;
use Illuminate\Support\Facades\Log;

class CustomerDetailsService
{
    public function getCustomerDetails(GetCustomerDetailsRequest $request): GetCustomerDetailsResponse
    {
        try {
            // Find customer by phone number (resource)
            $customer = Customer::where('Telephone_Number', $request->resource)->first();

            if (!$customer) {
                return new GetCustomerDetailsResponse(
                    null,
                    'UNREGISTERED',
                    null,
                    'SUCCESSFUL',
                    'Customer not found'
                );
            }

            $customerId = $customer->id;
            $productId = $request->productid;
            return $this->handleLoanDetailsRequest($customerId, $productId);
        } catch (\Exception $e) {
            Log::error('Get customer details error: ' . $e->getMessage(), [
                'resource' => $request->resource,
                'requesttype' => $request->requesttype,
                'error' => $e->getTraceAsString()
            ]);

            return new GetCustomerDetailsResponse(
                null,
                'UNREGISTERED',
                null,
                'FAILED',
                'Error retrieving customer details: ' . $e->getMessage()
            );
        }
    }

    private function handleLoanDetailsRequest(string $customerId, string $productCode): GetCustomerDetailsResponse
    {
        $partner = Partner::where('Identification_Code', 'CB011')->first();
        $loanProduct = LoanProduct::where('Code', $productCode)->where('partner_id', $partner->id)->first();
        if (!$loanProduct) {
            return new GetCustomerDetailsResponse(
                $customerId,
                'REGISTERED',
                null,
                'FAILED',
                'Loan product not found for code: ' . $productCode
            );
        }
        $loans = Loan::where('Customer_ID', $customerId)->where('partner_id', $partner->id)
            ->where('Loan_Product_ID', $loanProduct->id)
            ->get();
        $loanAccounts = [];
        foreach ($loans as $loan) {
            $due = new MoneyDetailsType(
                number_format($loan->totalOutstandingBalance(), 2, '.', ''),
                'UGX'
            );
            $loanAccounts[] = new LoanAccount(
                (string) $loan->id,
                $loan->mapToApiStatus($loan->Credit_Account_Status),
                $due,
                $loan->Maturity_Date ? $loan->Maturity_Date->format('Y-m-d') : '',
                (string) $loan->Term,
                'PERSONAL',
                number_format($loan->Interest_Rate, 2, '.', '')
            );
        }
        $creditLimit = $this->calculateCreditLimit($customerId);
        return new GetCustomerDetailsResponse(
            $customerId,
            'REGISTERED',
            $creditLimit,
            'SUCCESSFUL',
            'Customer details retrieved successfully',
            $loanAccounts
        );
    }

    private function calculateCreditLimit($customerId): MoneyDetailsType
    {
        $creditLimit = CreditLimit::where('customer_id', $customerId)->first();
        $baseLimit = 0;

        if ($creditLimit) {
            $baseLimit = max($baseLimit, $creditLimit->credit_limit);
        }

        return new MoneyDetailsType(
            number_format($baseLimit, 2, '.', ''),
            'UGX'
        );
    }
}
