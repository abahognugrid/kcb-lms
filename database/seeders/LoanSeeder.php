<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanApplication;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all loan applications to seed loans based on them
        $loanApplications = LoanApplication::all();

        // Create loans for each loan application
        foreach ($loanApplications as $loanApplication) {
            Loan::factory()->create([
                'Loan_Application_ID' => $loanApplication->id,
                'partner_id' => $loanApplication->partner_id,
                'Customer_ID' => $loanApplication->Customer_ID,
                'Loan_Product_ID' => $loanApplication->Loan_Product_ID,
            ]);
        }
    }
}
