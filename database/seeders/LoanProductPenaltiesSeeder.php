<?php

namespace Database\Seeders;

use App\Models\LoanProduct;
use App\Models\LoanProductPenalties;
use Illuminate\Database\Seeder;

class LoanProductPenaltiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LoanProductPenalties::create([
            'Name' => 'Late Penalty',
            'partner_id' => 1,
            'Calculation_Method' => 'Percentage',
            'Value' => 5,
            'Applicable_On' => 'Total Outstanding Balance',
            'Description' => 'Late Penalty',
            'Loan_Product_ID' => LoanProduct::first()->id,
        ]);

        LoanProductPenalties::create([
            'Name' => 'Old Late Penalty',
            'partner_id' => 1,
            'Calculation_Method' => 'Flat',
            'Value' => 30000,
            'Applicable_On' => 'Total Outstanding Balance',
            'Description' => 'Late Penalty',
            'Loan_Product_ID' => LoanProduct::first()->id,
        ]);
    }
}
