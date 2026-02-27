<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExternalAccount>
 */
class ExternalAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => \App\Models\Partner::factory(),
            'disbursement_account' => $this->faker->randomFloat(2, 100000, 5000000),
            'collection_account' => $this->faker->randomFloat(2, 50000, 2000000),
            'service_provider' => $this->faker->randomElement(['Airtel']),
        ];
    }
}
