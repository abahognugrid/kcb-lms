<?php

namespace App\Http\Controllers;

use App\Models\LoansImport;
use Illuminate\Http\Request;

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
                'amount_paid' => $data[8],
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
