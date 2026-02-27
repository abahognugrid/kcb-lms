<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Institution_Name' => fake()->unique()->company(),
            'Email_Address' => fake()->unique()->companyEmail(),
            'Identification_Code' => fake()->unique()->ean8(),
            'Telephone_Number' => fake()->unique()->e164PhoneNumber(),
            'License_Number' => fake()->unique()->ean13(),
            'License_Issuing_Date' => fake()->date(),
            'Institution_Type' => fake()->randomElement(['CB', 'MNO', 'VSLA', 'CI']),
            'Access_Type' => fake()->randomElement(['Loans'])
        ];
    }

    /**
     * Indicate that the partner should be created without triggering any events.
     */
    public function quiet(): static
    {
        return $this->afterMaking(function (Partner $partner) {
            Partner::withoutEvents(function () use ($partner) {
                $partner->save();
            });
        });
    }
}
