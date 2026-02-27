<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tax>
 */
class TaxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rate = null;
        $type = fake()->randomElement(['Flat', 'Percentage']);
        $amount = fake()->numberBetween(100, 20000);

        $partner = Partner::inRandomOrder()->first();

        $user = User::where("partner_id", $partner->id)->inRandomOrder()->first();

        if ($type == 'Percentage') {
            $rate = fake()->numberBetween(0, 30);
            $amount = null;
        }
        return [
            "name" => fake()->randomElement(['Government', 'Individual', 'VAT', 'Excise']),
            "description" => fake()->sentence(),
            "type" => $type,
            "rate" => $rate,
            "amount" => $amount,
            "is_active" => fake()->boolean(),
            "partner_id" => $partner->id,
            "created_by_id" => $user->id
        ];
    }
}
