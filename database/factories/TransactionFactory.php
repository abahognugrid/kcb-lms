<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Partner;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory()->createQuietly()->id,
            'Type' => fake()->randomElement([Transaction::DEPOSIT]),
            'Status' => 'Pending',
            'Telephone_Number' => Customer::factory()->createQuietly([
                'Telephone_Number' => '256' . fake()->numerify('#########'),
            ])->Telephone_Number,
            'Amount' => 10000,
            'TXN_ID' => Transaction::generateID()->toString(),
        ];
    }
}
