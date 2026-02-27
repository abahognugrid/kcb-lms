<?php

namespace Database\Factories;

use App\Models\LoanProduct;
use App\Models\LoanProductFee;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanProductFee>
 */
class LoanProductFeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Name' => fake()->name() . ' Fee',
            'Calculation_Method' => fake()->randomElement(LoanProductFee::CALCULATION_METHODS),
            'Loan_Product_ID' => LoanProduct::factory(),
            'partner_id' => Partner::factory(),
            'Value' => fake()->numberBetween(100, 100000),
            'Applicable_On' => fake()->randomElement(LoanProductFee::APPLICABLE_ON_OPTIONS),
            'Applicable_At' => fake()->randomElement(LoanProductFee::APPLICABLE_AT_OPTIONS),
            'Description' => fake()->sentence(),
        ];
    }
}
