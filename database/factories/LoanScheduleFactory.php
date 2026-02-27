<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Loan;
use App\Models\LoanSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanSchedule>
 */
final class LoanScheduleFactory extends Factory
{
    protected $model = LoanSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'installment_number' => 1,
            'payment_due_date' => now()->addDays(30),
            'principal' => 100000,
            'interest' => 10000,
            'total_payment' => 110000,
            'principal_remaining' => 100000,
            'interest_remaining' => 10000,
            'total_outstanding' => 110000,
            'type' => 'Loan',
            'payable_to' => null,
        ];
    }
}