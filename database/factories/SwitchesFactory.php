<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Switches;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class SwitchesFactory extends Factory
{
    protected $model = Switches::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::inRandomOrder()->first();

        return [
            'name' => fake()->name(),
            'partner_id' => $partner->id,
            'category' => 'Payment',
            'environment' => 'Test',
            'status' => 'On',
        ];
    }
}
