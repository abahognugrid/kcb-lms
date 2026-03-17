<?php

namespace App\Console\Commands;

use App\Models\CreditLimit;
use App\Models\Customer;
use App\Models\KCB\InitiateLoanApplicationRequest;
use App\Models\KCB\InitiateLoanRepaymentRequest;
use App\Models\Loan;
use App\Models\LoanPenalty;
use App\Models\LoansImport;
use App\Models\Partner;
use App\Services\KCB\LoanApplicationService;
use App\Services\KCB\LoanRepaymentService;
use Carbon\Carbon;
use Exception;
use Franzose\ClosureTable\Extensions\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCustomersAndLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:import-customers-and-loans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting customer import...');
        $rows = LoansImport::all();
        if ($rows->count() == 0) {
            throw new Exception('No customers found for import');
        }

        $partner = Partner::first();
        foreach ($rows as $row) {
            try {
                DB::beginTransaction();
                // Skip if customer already exists
                $customer = Customer::firstOrCreate(
                    ['Telephone_Number' => $row->telephone_number],
                    [

                        'First_Name' => $row->first_name,
                        'Last_Name' => $row->last_name,
                        'Other_Name' => $row->other_name,
                        'ID_Type' => $row->id_type,
                        'ID_Number' => $row->id_number,
                        'Classification' => 'Individual',
                        'Gender' => 'Male',
                        'Date_of_Birth' => '1990-01-01'
                    ]
                );

                CreditLimit::firstOrCreate(
                    [
                        'customer_id' => $customer->id,
                    ],
                    [
                        'partner_id' => $partner->id,
                        'credit_limit' => $row->loan_amount,
                        'used_credit' => 0,
                        'available_credit' => $row->loan_amount,
                        'Created_At' => Carbon::now(),
                        'Updated_At' => Carbon::now(),
                    ]
                );
                $loanRequest = new InitiateLoanApplicationRequest();
                $loanRequest->requestreference = Str::random(15);
                $loanRequest->resource = $row->telephone_number;
                $loanRequest->amount = $row->loan_amount;
                $loanRequest->tenor = 2;
                $loanRequest->due_date = $row->maturity_date;
                $loanRequest->currency = 'UGX';
                $loanRequest->loantype = 'PERSONAL';
                $loanRequest->productid = 'AG_SNL';
                $loanApplicationService = new LoanApplicationService();
                $response = $loanApplicationService->initiateLoanApplication($loanRequest);
                if ($response->status == 'FAILED') {
                    throw new Exception($response->message);
                }
                // Get Loan
                $loan = Loan::where('Customer_ID', $customer->id)->first();

                $totalExpected = $row->loan_amount + 3300;
                $amountPaid = $totalExpected - $row->outstanding_amount;

                if ($amountPaid > 0) {
                    $repaymentRequest = new InitiateLoanRepaymentRequest();
                    $repaymentRequest->requestreference = Str::random(15);
                    $repaymentRequest->accountholderid = $row->telephone_number;
                    $repaymentRequest->amount = $amountPaid;
                    $repaymentRequest->productid = 'AG_SNL';
                    $loanRepaymentService = new LoanRepaymentService();
                    $response = $loanRepaymentService->initiateLoanRepayment($repaymentRequest);
                    if ($response->status == 'FAILED') {
                        throw new Exception($response->message);
                    }
                }
                LoanPenalty::firstOrCreate([
                    'Loan_ID' => $loan->id,
                    'Customer_ID' => $loan->Customer_ID,
                    'date' => \Carbon\Carbon::parse($loan->Maturity_Date)->addDays(2),
                ], [
                    'partner_id' => $loan->partner_id,
                    'Amount' => 0,
                    'Amount_To_Pay' => 3000,
                    'Product_Penalty_ID' => 2,
                ]);

                $loan->update([
                    'Credit_Account_Status' => Loan::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS,
                    'Last_Status_Change_Date' => now()->format('Y-m-d'),
                ]);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $this->error($e->getMessage());
                return Command::FAILURE;
            }
        }
        $this->info(count($rows) . ' customers, loan applications, and loans imported successfully.');
        return Command::SUCCESS;
    }
}
