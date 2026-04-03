<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoansImport;
use App\Models\Partner;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanImportController extends Controller
{
    public function import(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'CSV file is required'
            ], 400);
        }

        $file = $request->file('file');

        if ($file->getClientOriginalExtension() !== 'csv') {
            return response()->json([
                'message' => 'Only CSV files are allowed'
            ], 400);
        }

        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        $header = fgetcsv($handle); // skip header row

        $rows = [];

        // Load all existing telephone numbers once to avoid multiple DB queries
        $existingPhones = LoansImport::pluck('telephone_number')->toArray();

        while (($data = fgetcsv($handle, 500, ",")) !== false) {
            $telephone = $data[0];

            // Skip if this telephone_number already exists
            if (in_array($telephone, $existingPhones)) {
                continue;
            }
            $rows[] = [
                'telephone_number' => $data[0],
                'first_name' => $data[1],
                'last_name' => $data[2],
                'other_name' => $data[3],
                'id_type' => $data[4],
                'id_number' => $data[5],
                'maturity_date' => $data[6],
                'loan_amount' => $data[7],
                'outstanding_amount' => $data[8],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Add this phone to the list so duplicates in the same CSV are skipped
            $existingPhones[] = $telephone;
        }

        fclose($handle);

        // Insert in chunks using Eloquent
        collect($rows)->chunk(500)->each(function ($chunk) {
            LoansImport::insert($chunk->toArray());
        });

        return response()->json([
            'message' => 'CSV imported successfully',
            'records' => count($rows)
        ]);
    }

    public function bulkCommissionRecovery(Request $request)
    {
        if (!$request->hasFile('file')) {
            return redirect()->back()->with('error', 'CSV file is required');
        }

        $file = $request->file('file');

        if ($file->getClientOriginalExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Only CSV files are allowed');
        }

        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return redirect()->back()->with('error', 'Unable to open file');
        }
        $header = fgetcsv($handle); // skip header row

        $partner = Partner::first(); // assuming single partner; otherwise pass in request

        $processed = 0;
        $successful = 0;
        $failed = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $phone = trim($row[0]);
            $amount = floatval(trim($row[1]));

            if (!$phone || $amount <= 0) {
                $failed++;
                $errors[] = [
                    'phone' => $phone,
                    'amount' => $amount,
                    'error' => 'Invalid phone or amount'
                ];
                continue;
            }

            // Normalize phone
            $phone = preg_replace('/\D/', '', $phone);
            if (str_starts_with($phone, '07')) {
                $phone = '256' . substr($phone, 1);
            }

            $processed++;

            try {
                DB::beginTransaction();

                $customer = Customer::where('Telephone_Number', $phone)->first();

                if (!$customer) {
                    $failed++;
                    $errors[] = [
                        'phone' => $phone,
                        'amount' => $amount,
                        'error' => 'Customer not found'
                    ];
                    DB::rollBack();
                    continue;
                }

                // Find active loan
                $loan = Loan::where('Customer_ID', $customer->id)
                    ->where('partner_id', $partner->id)
                    ->whereNot('Credit_Account_Status', Loan::ACCOUNT_STATUS_FULLY_PAID_OFF)
                    ->first();

                if (!$loan) {
                    $failed++;
                    $errors[] = [
                        'phone' => $phone,
                        'amount' => $amount,
                        'error' => 'No active loan found'
                    ];
                    DB::rollBack();
                    continue;
                }

                // Call your repayment function
                LoanService::initiateRepayment($partner, $customer, $amount, $loan);

                DB::commit();
                $successful++;
            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
                $errors[] = [
                    'phone' => $phone,
                    'amount' => $amount,
                    'error' => $e->getMessage()
                ];
                Log::error("Bulk repayment error: " . $e->getMessage());
                Log::error("Bulk repayment error: ", [$e]);
                return redirect()->back()->with('error', 'An error occurred during processing. Check logs for details.');
            }
        }

        fclose($handle);

        return redirect()->back()->with('success', "Bulk repayment process completed. Processed: $processed, Successful: $successful, Failed: $failed")->with('import_errors', $errors);
    }

    public function delinkedLoanRecovery(Request $request)
    {
        if (!$request->hasFile('file')) {
            return redirect()->back()->with('error', 'CSV file is required');
        }

        $file = $request->file('file');

        if ($file->getClientOriginalExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Only CSV files are allowed');
        }

        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return redirect()->back()->with('error', 'Unable to open file');
        }
        $header = fgetcsv($handle); // skip header row

        $partner = Partner::first(); // assuming single partner; otherwise pass in request

        $processed = 0;
        $successful = 0;
        $failed = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $phone = trim($row[0]);
            $amount = floatval(trim($row[1]));

            if (!$phone || $amount <= 0) {
                $failed++;
                $errors[] = [
                    'phone' => $phone,
                    'amount' => $amount,
                    'error' => 'Invalid phone or amount'
                ];
                continue;
            }

            // Normalize phone
            $phone = preg_replace('/\D/', '', $phone);
            if (str_starts_with($phone, '07')) {
                $phone = '256' . substr($phone, 1);
            }

            $processed++;

            try {
                DB::beginTransaction();

                $customer = Customer::where('Delinked_Phone_Number', $phone)->first();

                if (!$customer) {
                    $failed++;
                    $errors[] = [
                        'phone' => $phone,
                        'amount' => $amount,
                        'error' => 'Delinked Customer not found'
                    ];
                    DB::rollBack();
                    continue;
                }

                // Find active loan
                $loan = Loan::where('Customer_ID', $customer->id)
                    ->where('partner_id', $partner->id)
                    ->whereNot('Credit_Account_Status', Loan::ACCOUNT_STATUS_FULLY_PAID_OFF)
                    ->first();

                if (!$loan) {
                    $failed++;
                    $errors[] = [
                        'phone' => $phone,
                        'amount' => $amount,
                        'error' => 'No active loan found'
                    ];
                    DB::rollBack();
                    continue;
                }

                // Call your repayment function
                LoanService::initiateRepayment($partner, $customer, $amount, $loan);

                DB::commit();
                $successful++;
            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
                $errors[] = [
                    'phone' => $phone,
                    'amount' => $amount,
                    'error' => $e->getMessage()
                ];
                Log::error("Bulk repayment error: " . $e->getMessage());
                Log::error("Bulk repayment error: ", [$e]);

                return redirect()->back()->with('error', 'An error occurred during processing. Check logs for details.');
            }
        }

        fclose($handle);

        return redirect()->back()->with('success', "Delinked loan recovery process completed. Processed: $processed, Successful: $successful, Failed: $failed")->with('import_errors', $errors);
    }
}
