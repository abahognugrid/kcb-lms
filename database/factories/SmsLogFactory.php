<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Partner;
use App\Models\SmsLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class SmsLogFactory extends Factory
{
    protected $model = SmsLog::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::inRandomOrder()->first();
        $customer = Customer::inRandomOrder()->first();
        return [
            'Customer_ID' => $customer->id,
            'Message' => fake()->text(),
            'partner_id' => $partner->id,
            'Category' => fake()->randomElement(['Loan Disbursement', 'Loan Repayment',]),
            'Telephone_Number' => $customer->Telephone_Number,
            'Status' => 'Pending',
        ];
    }
}
