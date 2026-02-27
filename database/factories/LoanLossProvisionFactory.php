<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanLossProvision>
 */
class LoanLossProvisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => \App\Models\Partner::first()?->id ?? \App\Models\Partner::factory(),
            'loan_product_id' => \App\Models\LoanProduct::factory(),
            'ageing_category' => $this->faker->randomElement(['1-30 days', '31-60 days', '61-90 days', '91-180 days', '181+ days']),
            'ageing_category_slug' => function (array $attributes) {
                return \Illuminate\Support\Str::slug($attributes['ageing_category']);
            },
            'minimum_days' => $this->faker->numberBetween(1, 30), // Specify this manually where necessary
            'maximum_days' => $this->faker->numberBetween(31, 365), // Specify this manually where necessary
            'provision_rate' => $this->faker->randomFloat(2, 0, 100),
            'provision_amount' => $this->faker->numberBetween(0, 1000000),
            'arrears_amount' => $this->faker->numberBetween(0, 5000000),
            'batch_number' => 1,
            'created_by' => null,
            'approved_at' => null,
            'approved_by' => null,
        ];
    }
}
