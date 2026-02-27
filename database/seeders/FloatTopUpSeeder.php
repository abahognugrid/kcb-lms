<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\FloatTopUp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FloatTopUpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the directory exists
        if (!Storage::disk('public')->exists('proofs_of_payment')) {
            Storage::disk('public')->makeDirectory('proofs_of_payment');
        }

        foreach (Partner::all() as $partner) {
            $filePath = fake()->file(
                // The directory where faker will look for files
                base_path('public/assets/img/faker'),
                // Where the file should be copied to in the storage (optional)
                Storage::disk('public')->path('proofs_of_payment'),
                // Keep the file name
                false
            );
            $topup = FloatTopUp::create([
                'partner_id' => $partner->id,
                'Amount' => fake()->numberBetween(100000000, 500000000),
                'Proof_Of_Payment' => 'proofs_of_payment/' . basename($filePath), // Save relative path to storage
                'Status' => 'Approved',
            ]);
            $topup->saveJournalEntries();
        }
    }
}
