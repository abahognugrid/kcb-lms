<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\LmsUssdSessionTracking;
use App\Models\Partner;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class LmsUssdSessionTrackingFactory extends Factory
{
    protected $model = LmsUssdSessionTracking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requestId' => fake()->uuid(),
            'Customer_Phone_Number' => Customer::factory()->createQuietly([
                'Telephone_Number' => fake("UG")->phoneNumber(),
            ])->Telephone_Number,
        ];
    }
}
