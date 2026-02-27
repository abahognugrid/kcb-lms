<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanRepayment>
 */
class LoanRepaymentFactory extends Factory
{
    protected $model = LoanRepayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Loan_ID' => Loan::factory(),
            'partner_id' => Partner::factory(),
            'Customer_ID' => Customer::factory(),
            'Transaction_ID' => null,
            'amount' => 100000,
            'Principal' => 90000,
            'Interest' => 10000,
            'Fee' => 0,
            'Penalty' => 0,
            'Transaction_Date' => now(),
            'Last_Payment_Date' => now()->toDateString(),
            'Last_Payment_Amount' => '100000',
            'Current_Balance_Amount' => '0',
            'Current_Balance_Amount_UGX_Equivalent' => '0',
            'partner_notified' => false,
            'partner_notified_date' => null,
        ];
    }
}
