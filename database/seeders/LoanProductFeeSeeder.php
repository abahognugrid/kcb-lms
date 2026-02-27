<?php

namespace Database\Seeders;

use App\Models\LoanProductFee;
use Illuminate\Database\Seeder;

class LoanProductFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LoanProductFee::create([
            'Name' => 'Application Fee',
            'partner_id' => 1,
            'Calculation_Method' => 'Percentage',
            'Value' => 1.5,
            'Applicable_On' => 'Principal',
            'Applicable_At' => 'Disbursement',
            'Description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum non suscipit ante. Sed quis porta metus. Aliquam faucibus ac mi eu elementum.',
            'Loan_Product_ID' => 1,
        ]);
    }
}
