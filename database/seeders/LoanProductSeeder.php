<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\LoanProduct;
use App\Models\LoanProductType;
use App\Models\LoanProductTerm;
use Illuminate\Database\Seeder;

class LoanProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Partner::all() as $partner) {
            $loanProduct = LoanProduct::create([
                'Name' => 'Agent Loan',
                'Code' => 'AG_SNL',
                'partner_id' => $partner->id,
                'Loan_Product_Type_ID' => LoanProductType::first()->id,
                'Minimum_Principal_Amount' => 5000,
                'Default_Principal_Amount' => 30000,
                'Maximum_Principal_Amount' => 1000000,
                'Decimal_Place' => 0,
                'Round_UP_or_Off_all_Interest' => 1,
                'Repayment_Order' => json_encode(['Penalty', 'Interest', 'Principal', 'Fees']),
                'Enrollment_Type' => 'Individual',
                'Auto_Debit' => 'Yes'
            ]);

            LoanProductTerm::create([
                'partner_id' => $partner->id,
                'Loan_Product_ID' => $loanProduct->id,
                'Interest_Rate' => 2,
                'Interest_Calculation_Method' => 'Flat',
                'Repayment_Cycles' => json_encode(['Once']),
                'Value' => 7,
                'Interest_Cycle' => 'None',
            ]);

            LoanProductTerm::create([
                'partner_id' => $partner->id,
                'Loan_Product_ID' => $loanProduct->id,
                'Interest_Rate' => 4,
                'Interest_Calculation_Method' => 'Flat',
                'Repayment_Cycles' => json_encode(['Once']),
                'Value' => 14,
                'Interest_Cycle' => 'None',
            ]);

            LoanProductTerm::create([
                'partner_id' => $partner->id,
                'Loan_Product_ID' => $loanProduct->id,
                'Interest_Rate' => 300,
                'Interest_Calculation_Method' => 'Flat on Loan Amount',
                'Repayment_Cycles' => json_encode(['Once']),
                'Value' => 2,
                'Interest_Cycle' => 'None',
            ]);
        }
    }
}
