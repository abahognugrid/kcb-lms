<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoansImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function bulkDelink(Request $request)
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
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return response()->json(['message' => 'Unable to open file'], 500);
        }

        $header = fgetcsv($handle); // skip header row

        $delinked = 0;
        $notFound = 0;
        $processed = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $phone = trim($row[0]);
                if (!$phone) continue;

                $phone = preg_replace('/\D/', '', $phone);
                if (str_starts_with($phone, '07')) {
                    $phone = '256' . substr($phone, 1);
                }

                $processed++;

                $customer = Customer::where('Telephone_Number', $phone)
                    ->first();

                if ($customer) {
                    $customer->update([
                        'Is_Delinked' => true,
                        'Delinked_At' => now(),
                        'Delinked_Phone_Number' => $phone,
                        'Telephone_Number' => null,
                    ]);
                    $delinked++;
                } else {
                    $notFound++;
                }
            }

            fclose($handle);
            DB::commit();

            return response()->json([
                'message' => 'Delink process completed',
                'summary' => [
                    'processed' => $processed,
                    'delinked' => $delinked,
                    'not_found' => $notFound,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            return response()->json([
                'message' => 'Error processing file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
