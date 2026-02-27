<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Partner::create([
            'Identification_Code' => 'CB011',
            'Institution_Name' => 'KCB Bank',
            'License_Issuing_Date' => '2020-01-01',
            'License_Number' => '123456',
            'Telephone_Number' => '256700000000',
            'Email_Address' => 'info@kcb.com',
            'Access_Type' => 'Loans',
            'Accounting_Type' => 'Normal',
        ]);
    }
}
