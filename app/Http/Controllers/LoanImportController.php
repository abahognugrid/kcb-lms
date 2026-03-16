<?php

namespace App\Http\Controllers;

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
                'gender' => $data[4],
                'date_of_birth' => $data[5],
                'id_type' => $data[6],
                'id_number' => $data[7],
                'loan_application_date' => $data[8],
                'maturity_date' => $data[9],
                'loan_amount' => $data[10],
                'amount_paid' => $data[11],
                'loan_penalty' => $data[12],
                'outstanding_amount' => $data[13],
                'loan_status' => $data[14],
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
}
