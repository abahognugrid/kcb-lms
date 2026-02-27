<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'First_Name' => fake()->firstName(),
            'Last_Name' => fake()->lastName(),
            'Other_Name' => fake()->firstName(),
            'Gender' => fake()->randomElement(['Male', 'Female']),
            'Marital_Status' => fake()->randomElement(['Single', 'Married', 'Divorced', 'Separated']),
            'Email_Address' => fake()->unique()->safeEmail(),
            'ID_Type' => fake()->randomElement(['Country_ID', 'Passport_Number', 'Refugee_Number']),
            'ID_Number' => fake()->ean13(),
            'Telephone_Number' => fake()->unique()->e164PhoneNumber(),
            'Date_of_Birth' => fake()->dateTimeBetween('-80 years', 'now')->format('Y-m-d')
        ];
    }

    public function withOptions(array $customOptions = [])
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterMaking(function (Customer $customer) use ($customOptions) {
            $options = array_merge([
                'loanaccounts' => [
                    'loanaccount' => [
                        'accountnumber' => null,
                        'status' => 'APPROVED',
                        'due' => [
                            'amount' => 0,
                            'currency' => 'UGX'
                        ],
                        'duedate' => now()->tomorrow()->toDateString(),
                        'loantype' => 'PERSONAL',
                    ]
                ]
            ], $customOptions);
            $customer->options = $options;
            $customer->save();
        });
    }
}
