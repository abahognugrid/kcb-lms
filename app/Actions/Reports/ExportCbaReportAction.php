<?php

namespace App\Actions\Reports;

use App\Enums\LoanAccountType;
use App\Enums\LoanApplicationStatus;
use App\Enums\MaritalStatus;
use App\Enums\PaymentFrequency;
use App\Models\Loan;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Writer;

class ExportCbaReportAction
{
    /**
     * @throws InvalidArgument
     */
    public function execute()
    {
        $path = 'partners/' . auth()->user()->partner->Identification_Code . '/' . $this->getCBAFilename();
        $csvFile = Writer::createFromString()->setDelimiter('|')->setEnclosure('"');

        $filters = [
            'startDate' => now()->subMonth()->startOfMonth()->format('Y-m-d'),
            'endDate' => now()->subMonth()->endOfMonth()->format('Y-m-d'),
        ];

        $recordQuery = app(GetCreditBorrowerAccountReportDetailsAction::class)
            ->filters($filters)
            ->execute();

        $headerRecord = $this->getHeaderRecord();

        try {
            $csvFile->insertOne($headerRecord);

            $recordQuery->lazy()
                ->each(function (Loan $loan) use ($csvFile) {
                    $csvFile->insertOne($this->getCbaRecord($loan));
                });

            Storage::disk('local')->put($path, $csvFile->toString());

            return $path;
        } catch (CannotInsertRecord|Exception $e) {
            Log::debug($e->getMessage());

            return '';
        }
    }

    /**
     * @return array
     */
    public function getHeaderRecord(): array
    {
        return [
            "H",
            auth()->user()->partner->Identification_Code,
            auth()->user()->partner->Institution_Name,
            now()->subMonth()->endOfMonth()->format('Ymd'),
            "8.0",
            now()->format('Ymd'),
            "CBA"
        ];
    }

    private function getCBAFilename(): string
    {
        return auth()->user()->partner->Identification_Code .
            now()->subMonth()->endOfMonth()->format('Ymd') .
            'CBA.CSV';
    }

    private function getLoanStatusCode(?int $status): ?int
    {
        return match ($status) {
            Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS => 0, // Active/Current
            Loan::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS => 1, // In arrears
            Loan::ACCOUNT_STATUS_WRITTEN_OFF => 3, // Written off
            Loan::ACCOUNT_STATUS_FULLY_PAID_OFF => 4, // Fully paid
            default => null
        };
    }

    private function getInterestTypeCode(string $interestCalculationMethod): int
    {
        if ($interestCalculationMethod === 'Flat') {
            return 0;
        } else {
            return 1;
        }
    }

    private function getInterestCalculationMethodCode(string $interestCalculationMethod): int
    {
        if ($interestCalculationMethod === 'Flat') {
            return 1;
        } else {
            return 0;
        }
    }

    private function getCbaRecord(Loan $loan): array
    {
        $repayments = $loan->loan_repayments;

        return [
            "D",
            auth()->user()->partner->Identification_Code, // pi_identification_code
            "001", // Branch identification code
            $loan->Customer_ID, // Borrowers client number
            "0", // Borrower classification
            $loan->account_number, // Credit account reference
            $loan->Credit_Account_Date->format('Ymd'), // Credit account date
            $loan->Credit_Amount, // Credit amount
            $loan->Credit_Amount, // Credit amount ugx equivalent
            $loan->Facility_Amount_Granted, // Facility amount granted
            $loan->Credit_Amount_Drawdown, // Credit amount drawdown
            $loan->Credit_Amount_Drawdown, // Credit amount drawdown ugx equivalent
            $loan->Credit_Account_Type,
            null,
            $loan->Credit_Account_Date->format('Ymd'), // Transaction date
            $loan->Currency, // Currency
            '0', // Opening balance indicator
            $loan->Maturity_Date->format('Ymd'), // Maturity date
            $loan->Type_of_Interest, // Type of interest
            $this->getInterestCalculationMethodCode($loan->loan_term->Interest_Calculation_Method), // Interest calculation method
            $loan->Annual_Interest_Rate_at_Disbursement, // Annual interest rate at disbursement
            $loan->Annual_Interest_Rate_at_Disbursement, // Annual interest rate at reporting
            $repayments->first()?->Transaction_Date->format('Ymd'), // Date of first payment
            $loan->Credit_Amortization_Type, // Credit amortization type
            PaymentFrequency::getValueFromName($loan->Credit_Payment_Frequency),
            $loan->Number_of_Payments,
            $loan->schedule->groupBy('installment_number')->first()->sum('total_payment'), // Monthly instalment amount
            ($balance = $loan->totalOutstandingBalance()), // Current balance amount
            $balance, // Current balance amount ugx equivalent
            '0', // Current balance indicator
            $repayments->last()?->Transaction_Date->format('Ymd'), // Last payment date
            $repayments->last()?->amount, // Last payment amount
            $loan->Credit_Account_Status, // Credit account status
            $loan->Last_Status_Change_Date->format('Ymd'), // Last status change date
            '5', // Credit account risk classification // todo: get risk classification
            $loan->Credit_Account_Status === LoanAccountType::WrittenOff->value ? $loan->Written_Off_Date?->format('Ymd') : null, // Credit account arrears date
            $loan->Credit_Account_Status === LoanAccountType::WrittenOff->value ? $loan->Written_Off_Date?->diffInDays($loan->Maturity_Date, true) : null, // Number of days in arrears
            $loan->Credit_Account_Status === LoanAccountType::WrittenOff->value ? $loan->Written_Off_Amount - $loan->Written_Off_Amount_Recovered : 0, // Balance overdue
            '1', // Flag for restructured credit
            null, // Old branch code
            null, // Old account number
            null, // Old client number
            null, // Old pi identification code
            $loan->Credit_Account_Closure_Date?->format('Ymd'), // Credit account closure date
            $loan->Credit_Account_Closure_Reason, // Credit account closure reason
            null, // Specific provision amount
            'Y', // Client consent flag
            'Y', // Client advice notice flag
            $loan->Term,
            '1207',
            null, // Group joint account
            '1', // Flag for group
            '1', // Flag for joint account
            null, // Mode of restructure
            '0', // Risk classification criteria
            null, // Registration certificate number
            null, // Tax identification number
            null, // FCS number
            null, // Passport number
            null, // Driver's license ID number
            null, // Driver's license permit number
            $loan->customer->ID_Number,
            'UG', // Country issuing authority
            'UG', // Nationality
            'UG', // Country of issue
            null, // Refugee number
            null, // Work permit number
            null, // Business name
            null, // Trading name
            null, // Activity description
            null, // Industry sector code
            null, // Date registered
            null, // Business type code
            $loan->customer->Last_Name,
            $loan->customer->First_Name,
            $loan->customer->Other_Name,
            null, // Forename3
            strtolower($loan->customer->Gender) === 'male' ? 0 : 1,
            MaritalStatus::getValueFromName($loan->customer->Marital_Status),
            $loan->customer->Date_of_Birth->format('Ymd'),
            3, // Employment type
            null, // Primary occupation
            null, // Employer name
            null, // Employee number
            null, // Employment date
            null, // Income band
            null, // Salary frequency
            null, // Unit number
            null, // Building name
            null, // Floor number
            null, // Plot or street number
            null, // LC or street name
            'KAMPALA', // Parish
            'KAMPALA', // Suburb
            'KAMPALA', // Village
            'KAMPALA', // County or town
            'KAMPALA', // District
            '0', // Region
            null, // PO box number
            null, // Post office town
            'UG', // Country code
            '1', // Period at address
            'T', // Flag of ownership
            '256', // Primary number country dialling code
            str($loan->customer->Telephone_Number)->after('256')->toString(),
            null, // Other number country dialling code
            null, // Other number telephone number
            null, // Mobile number country dialling code
            null, // Mobile number telephone number
            null, // Facsimile country dialling code
            null, // Facsimile number
            null, // Email address
            null, // Web site
            null, // Latitude
            null, // Longitude
            null, // Secondary contact unit number
            null, // Secondary contact unit name
            null, // Secondary contact floor number
            null, // Secondary contact plot or street number
            null, // Secondary contact LC or street name
            null, // Secondary contact parish
            null, // Secondary contact suburb
            null, // Secondary contact village
            null, // Secondary contact county or town
            null, // Secondary contact district
            null, // Secondary contact region
            null, // Secondary contact PO box number
            null, // Secondary contact post office town
            null, // Secondary contact country code
            null, // Secondary contact period at address
            null, // Secondary contact flag for ownership
            '256', // Secondary contact primary number country dialling code
            str($loan->customer->Telephone_Number)->after('256')->toString(),
            null, // Secondary contact other number country dialling code
            null, // Secondary contact other number telephone number
            null, // Secondary contact mobile number country dialling code
            null, // Secondary contact mobile number telephone number
            null, // Secondary contact facsimile country dialling code
            null, // Secondary contact facsimile number
            null, // Secondary contact email address
            null, // Secondary contact website
            null, // Secondary contact latitude
            null, // Secondary contact longitude
        ];
    }
}
