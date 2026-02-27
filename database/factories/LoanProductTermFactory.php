<?php

namespace Database\Factories;

use App\Models\LoanProduct;
use App\Models\LoanProductTerm;
use App\Models\LoanProductType;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanProduct>
 */
class LoanProductTermFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanProductTerm::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Interest_Rate' => 10,
            'Interest_Calculation_Method' => 'Flat',
            'Value' => 3,
            'Repayment_Cycles' => '["Monthly"]',
            'Code' => 'LPT-' . fake()->unique()->ean8(),
            'Interest_Cycle' => 'Monthly',
        ];
    }
}
