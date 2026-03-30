<?php

namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $ussdCode = config('lms.ussd_code');
        $templates = [
            [
                'partner_id' => 1,
                'Loan_Product_ID' => 1,
                'Day' => -1, // 1 day before due date
                'Template' => 'Dear Customer, your :ProductName of UGX :Amount is due tomorrow. Please dial ' . $ussdCode . ' to repay on time and avoid penalties.',
            ],
            [
                'partner_id' => 1,
                'Loan_Product_ID' => 1,
                'Day' => 0, // on due date
                'Template' => 'Reminder: Your :ProductName loan of UGX :Amount is due today (:Date). Please dial ' . $ussdCode . ' to make your payment and avoid penalties.',
            ],
            [
                'partner_id' => 1,
                'Loan_Product_ID' => 1,
                'Day' => 1, // 1 day after due date
                'Template' => 'Alert: Your :ProductName loan of UGX :Amount is overdue. A 5% daily penalty has beed applied. Dial ' . $ussdCode . ' to repay now and minimize penalties.',
            ],
        ];

        foreach ($templates as $data) {
            SmsTemplate::create($data);
        }
    }
}
