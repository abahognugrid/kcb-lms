<?php

namespace Database\Factories;

use App\Models\LoanProduct;
use App\Models\LoanProductPenalties;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanProductPenalties>
 */
class LoanProductPenaltiesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanProductPenalties::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'Name' => fake()->name(),
            'Calculation_Method' => fake()->randomElement(LoanProductPenalties::CALCULATION_METHODS),
            'Value' => fake()->numberBetween(10, 10000),
            'Applicable_On' => fake()->randomElement(LoanProductPenalties::PENALTY_APPLICATION_FORMS),
            'Description' => fake()->sentence(),
            'Loan_Product_ID' => LoanProduct::factory(),
        ];
    }
}
