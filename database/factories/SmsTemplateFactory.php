<?php

namespace Database\Factories;

use App\Models\LoanProduct;
use App\Models\Partner;
use App\Models\SmsTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class SmsTemplateFactory extends Factory
{
    protected $model = SmsTemplate::class;

    /**
     * todo: Move scenario logic to the tests themselves or configure states.
     *
     * @return array|mixed[]
     */
    public function definition(): array
    {
        $partner = Partner::inRandomOrder()->first();
        $loanProduct = LoanProduct::where('partner_id', $partner->id)->first();

        if (!$loanProduct) {
            return [];
        }

        // Get an array of existing 'Day' values for the selected 'Loan_Product_ID'
        $existingDays = SmsTemplate::where('Loan_Product_ID', $loanProduct->id)->pluck('Day')->toArray();

        // Generate a unique 'Day' value not in the existingDays array
        $availableDays = array_diff([-3, 0, 5], $existingDays);

        if (empty($availableDays)) {
            // If all possible 'Day' values are already used for this 'Loan_Product_ID', skip this iteration
            return [];
        }

        $day = fake()->randomElement($availableDays);

        return [
            'Loan_Product_ID' => $loanProduct->id,
            'Template' => 'Please pay your loan of UGX  :Amount before :Date to :Partner',
            'partner_id' => $partner->id,
            'Day' => $day,
        ];
    }
}
