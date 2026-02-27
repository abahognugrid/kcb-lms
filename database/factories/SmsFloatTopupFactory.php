<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\SmsFloatTopup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class SmsFloatTopupFactory extends Factory
{
    protected $model = SmsFloatTopup::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::inRandomOrder()->first();
        return [
            'Status' => fake()->randomElement(['Approved', 'Pending', 'Rejected']),
            'partner_id' => $partner->id,
            'Amount' => fake()->numberBetween(30000, 100000),
            'Proof_Of_Payment' => fake()->image(storage_path('app/public/sms-float-topups'), 640, 480, null, false),
        ];
    }
}
