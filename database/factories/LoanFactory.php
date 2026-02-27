<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProductTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Fetch a random loan application
        $loanApplication = LoanApplication::factory()->create();
        // Define loan details based on the loan application
        $facilityAmountGranted = $loanApplication->Amount;
        $creditAmountDrawdown = fake()->randomFloat(2, 500, $facilityAmountGranted);
        $interestRate = fake()->randomFloat(2, 5, 20); // Random interest rate between 5% and 20%
        $loanTerm = LoanProductTerm::factory()->create([
            'partner_id' => $loanApplication->partner_id,
            'Loan_Product_ID' => $loanApplication->Loan_Product_ID,
        ]);

        $number_of_installments = fake()->numberBetween(1, 4);
        $frequency_of_installments = fake()->randomElement(json_decode($loanTerm->Repayment_Cycles, true));
        $Credit_Account_Status = fake()->randomElement([1, 4, 5]);

        $Credit_Account_Closure_Date = null;
        $Credit_Account_Closure_Reason = null;

        if ($Credit_Account_Status == 4) {
            $Credit_Account_Closure_Date = now();
            $Credit_Account_Closure_Reason = 'Loan has been fully paid';
        }

        return [
            'partner_id' => $loanApplication->partner_id,
            'Customer_ID' => $loanApplication->Customer_ID,
            'Loan_Product_ID' => $loanApplication->Loan_Product_ID,
            'Loan_Application_ID' => $loanApplication->id,
            'Credit_Application_Status' => 'Approved',
            'Last_Status_Change_Date' => now(),
            'Credit_Account_Reference' => fake()->uuid(),
            'Credit_Account_Date' => now()->subDays(30), // 30 days ago as default
            'Credit_Amount' => $facilityAmountGranted,
            'Facility_Amount_Granted' => $facilityAmountGranted,
            'Credit_Amount_Drawdown' => $creditAmountDrawdown,
            'Credit_Account_Type' => 14, // Mobile Loan. See DSM APPENDIX 1.5 for more details
            'Currency' => 'UGX', // Default currency
            'Maturity_Date' => Loan::determineMaturityDate(
                $number_of_installments,
                $frequency_of_installments,
                $loanTerm->Value,
            ), // Maturity date based on loan term
            'Annual_Interest_Rate_at_Disbursement' => $interestRate,
            'Date_of_First_Payment' => Loan::determineDateOfFirstPayment($frequency_of_installments, $loanTerm->Value), // First payment due in frequency chosen
            'Credit_Amortization_Type' => fake()->randomElement([0, 1, 2, 3]), // See DSM APPENDIX 1.11 for more details
            'Credit_Payment_Frequency' => 'Monthly',
            // 'Credit_Payment_Frequency' => fake()->numberBetween(0, 12),
            'Number_of_Payments' => fake()->numberBetween(1, int2: 6), // Number of payments based on term and frequency
            'Instalment_Amount' => fake()->randomFloat(2, 1000, 5000), // This should be calculated based on term
            'Credit_Account_Closure_Date' => $Credit_Account_Closure_Date,
            'Credit_Account_Closure_Reason' => $Credit_Account_Closure_Reason,
            'Specific_Provision_Amount' => null, // Optional
            'Client_Consent_Flag' => $loanApplication->Client_Consent_flag,
            'Client_Advice_Notice_Flag' => 'Yes',
            'Type_of_Interest' => fake()->randomElement(['Flat', 'Reducing Balance']),
            'Loan_Term_ID' => $loanTerm->id,
            'Interest_Rate' => $loanTerm->Interest_Rate,
            'Interest_Calculation_Method' => $loanTerm->Interest_Calculation_Method,
            'Term' => $loanTerm->Value,
            'Credit_Account_Status' => $Credit_Account_Status,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Loan $loan) {

            $loan->schedule()->create([
                'installment_number' => 1,
                'principal' => $loan->Credit_Amount,
                'interest' => 5000,
                'total_payment' => $loan->Credit_Amount + 5000,
                'principal_remaining' => $loan->Credit_Amount,
                'interest_remaining' => 5000,
                'total_outstanding' => $loan->Credit_Amount + 5000,
                'payment_due_date' => $loan->Maturity_Date->toDateString(),
                'type' => 'Loan'
            ]);
        });
    }
}
