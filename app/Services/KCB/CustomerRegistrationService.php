<?php
// app/Services/CustomerRegistrationService.php

namespace App\Services\KCB;

use App\Models\Customer;
use App\Models\SavingsAccount;
use App\Models\KCB\SavingsAccountResponse;
use App\Models\KCB\CustomerRegistrationRequest as RegistrationRequest;
use App\Models\KCB\CustomerRegistrationResponse;
use App\Models\Partner;
use App\Models\SavingsProduct;
use App\Notifications\SmsNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerRegistrationService
{
    public function registerCustomer(RegistrationRequest $request): CustomerRegistrationResponse
    {
        DB::beginTransaction();

        try {
            // Map the Airtel API fields to your database fields
            $customerData = $this->mapToCustomerModel($request);

            $existingNationalID = Customer::where('ID_Number', $customerData['ID_Number'])->first();
            if ($existingNationalID) {
                $phoneNumbers = Customer::where('ID_Number', $customerData['ID_Number'])->pluck('Telephone_Number')->toArray();
                // phone numbers should not be more than 3 for a single national ID
                if (count($phoneNumbers) >= 5) {
                    return new CustomerRegistrationResponse(
                        null,
                        null,
                        'FAILED',
                        'Customer with this National ID already has 3 registered phone numbers: ' . implode(', ', $phoneNumbers)
                    );
                }
            }

            // Check if customer already exists
            $existingCustomer = Customer::where('Telephone_Number', $customerData['Telephone_Number'])->first();

            if ($existingCustomer) {
                // Check if customer already has a savings account
                $existingSavingsAccount = SavingsAccount::where('customer_id', $existingCustomer->id)->first();

                if ($existingSavingsAccount) {
                    $savingsAccountResponse = new SavingsAccountResponse(
                        $existingSavingsAccount->id,
                        $existingSavingsAccount->is_active ? 'ACTIVE' : 'INACTIVE',
                        number_format($existingSavingsAccount->current_balance, 2, '.', ''),
                        $existingSavingsAccount->currency,
                        $existingSavingsAccount->savings_account_type ?? 'PERSONAL'
                    );

                    return new CustomerRegistrationResponse(
                        $existingCustomer->id,
                        $savingsAccountResponse,
                        'SUCCESSFUL',
                        'Customer and savings account already exist'
                    );
                }

                // Create savings account for existing customer
                $savingsAccount = $this->createSavingsAccount($existingCustomer->id);
                $savingsAccountResponse = $this->mapToSavingsAccountResponse($savingsAccount);

                DB::commit();



                return new CustomerRegistrationResponse(
                    $existingCustomer->id,
                    $savingsAccountResponse,
                    'SUCCESSFUL',
                    'Savings account created for existing customer'
                );
            }

            // Create new customer
            $customer = Customer::create($customerData);

            // Create savings account for new customer
            $savingsAccount = $this->createSavingsAccount($customer->id);
            $savingsAccountResponse = $this->mapToSavingsAccountResponse($savingsAccount);

            DB::commit();

            Log::info('Customer and savings account registered successfully', [
                'customer_id' => $customer->id,
                'savings_account_id' => $savingsAccount->id,
                'airtel_reference' => $request->requestreference
            ]);

            return new CustomerRegistrationResponse(
                'CUST' . str_pad($customer->id, 8, '0', STR_PAD_LEFT),
                $savingsAccountResponse,
                'SUCCESSFUL',
                'Customer and savings account registered successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Customer registration failed: ' . $e->getMessage(), [
                'request_reference' => $request->requestreference ?? 'unknown',
                'error' => $e->getTraceAsString()
            ]);

            return new CustomerRegistrationResponse(
                null,
                null,
                'FAILED',
                'Registration failed: ' . $e->getMessage()
            );
        }
    }

    private function createSavingsAccount(int $customerId): SavingsAccount
    {
        $partnerId = Partner::where('Identification_Code', 'CB011')->first()->id;
        $savingsProductID = SavingsProduct::where('partner_id', $partnerId)->first()->id;

        return SavingsAccount::create([
            'partner_id' => $partnerId,
            'customer_id' => $customerId,
            'savings_product_id' => $savingsProductID,
            'expected_amount' => 0.0,
        ]);
    }

    private function mapToSavingsAccountResponse(SavingsAccount $savingsAccount): SavingsAccountResponse
    {
        return new SavingsAccountResponse(
            $savingsAccount->id
        );
    }

    private function mapToCustomerModel(RegistrationRequest $request): array
    {
        return [
            'First_Name' => $request->firstname,
            'Last_Name' => $request->lastname,
            'Other_Name' => $request->middlename ?? null,
            'Gender' => $this->mapGender($request->gender),
            'Marital_Status' => null,
            'Date_of_Birth' => $this->parseDate($request->dob),
            'ID_Type' => $request->idtype,
            'ID_Number' => $request->idnumber,
            'Telephone_Number' => $request->resource,
        ];
    }

    private function mapGender(string $airtelGender): string
    {
        return match (strtoupper($airtelGender)) {
            'MALE' => 'Male',
            'FEMALE' => 'Female',
            default => 'Other'
        };
    }

    private function parseDate(string $dateString): string
    {
        $date = \DateTime::createFromFormat('d/m/Y', $dateString);
        if ($date) {
            return $date->format('Y-m-d');
        }

        throw new \Exception('Invalid date format. Expected dd/mm/yyyy');
    }
}
