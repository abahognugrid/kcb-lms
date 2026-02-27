<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\SmsCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SmsCampaign>
 */
class SmsCampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'name' => fake()->name(),
            'message' => fake()->sentence(),
            'target_group' => fake()->randomElement(SmsCampaign::TARGET_GROUPS),
        ];
    }
}
