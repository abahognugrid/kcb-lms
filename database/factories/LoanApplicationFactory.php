<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Customer;
use App\Models\LoanProduct;
use App\Models\LoanApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanApplication>
 */
class LoanApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer = Customer::factory()->create();
        $loan_product = LoanProduct::factory()->createQuietly();
        $purpose = fake()->randomElement(["Personal", "Business", "Education", "Hea`lth", "Other"]);
        $status = fake()->randomElement(['Pending', 'Approved', 'Rejected']);

        $rejection_reason = null;
        if ($status == 'Rejected') {
            $rejection_reason = fake()->randomElement(['Bad history', 'Not married', 'Below age limit', 'Has outstanding loan', 'Serial defaulter']);
        }

        $has_client_consented = fake()->randomElement(['Yes', 'No']);

        if ($status == 'Approved') {
            $has_client_consented = 'Yes';
        }

        return [
            'partner_id' => $loan_product->partner->id,
            'Customer_ID' => $customer->id,
            'Loan_Product_ID' => $loan_product->id,
            'Loan_Purpose' => $purpose,
            'Client_Number' => fake()->numerify('GG-###'),
            'Credit_Application_Reference' => fake()->uuid(),
            'Applicant_Classification' => fake()->randomElement(['Individual', 'Non-Individual']),
            'Credit_Application_Date' => fake()->date(),
            'Amount' => 50000,
            'Currency' => 'UGX',
            'Credit_Account_or_Loan_Product_Type' => $purpose,
            'Credit_Application_Status' => $status,
            'Last_Status_Change_Date' => fake()->date(),
            'Credit_Application_Duration' => fake()->numberBetween(1, 180),
            'Rejection_Reason' => $rejection_reason,
            'Client_Consent_flag' => $has_client_consented,
            'Group_Identification_Joint_Account_Identification' => fake()->optional()->bothify('ID##??'),
            'Credit_Amount_Approved' => fake()->randomFloat(2, 1000, 500000),
            'Currency_Approved' => 'UGX',
            'PCI_Country_Code' => 'UG',
            'PCI_Flag_of_Ownership' => fake()->randomElement(['Owner', 'Tenant', 'Other']),
            'PCI_Period_At_Address' => fake()->numberBetween(1, 20),
            'Country' => 'Uganda',
            'District' => fake()->city(),
            'Subcounty' => fake()->word(),
            'Parish' => fake()->word(),
            'Village' => fake()->word(),
        ];
    }
}
