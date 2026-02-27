<?php

namespace Database\Factories;

use App\Models\LoanProduct;
use App\Models\LoanProductType;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanProduct>
 */
class LoanProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'Code' => 'LP-' . fake()->unique()->ean8(),
            'Name' => fake()->words(3, true) . ' Loan',
            'Loan_Product_Type_ID' => (LoanProductType::create([
                'name' => 'Mobile Loan',
                'code' => '1001',
            ]))->id,
            'Minimum_Principal_Amount' => 10000,
            'Default_Principal_Amount' => 50000,
            'Maximum_Principal_Amount' => 500000,
            'Auto_Debit' => 'No',
            'Decimal_Place' => 0,
            'Round_UP_or_Off_all_Interest' => 1,
            'Repayment_Order' => ["Interest", "Fees", "Principal"],
            'Arrears_Auto_Write_Off_Days' => 180,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (LoanProduct $loanProduct) {
            $loanProduct->loan_product_terms()->create([
                'partner_id' => $loanProduct->partner_id,
                'Loan_Product_ID' => $loanProduct->id,
                'Interest_Rate' => 10,
                'Interest_Calculation_Method' => 'Flat',
                'Repayment_Cycles' => json_encode(['Monthly']),
                'Value' => 30,
                'Code' => "LPT$loanProduct->id-" . strtoupper(uniqid()),
                'Interest_Cycle' => 'Monthly',
            ]);
        });
    }
}
