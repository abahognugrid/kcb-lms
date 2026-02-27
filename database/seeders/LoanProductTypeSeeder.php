<?php

namespace Database\Seeders;

use App\Models\LoanProductType;
use Illuminate\Database\Seeder;

class LoanProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LoanProductType::create([
            'Name' => 'Mobile Loan',
            'Code' => '14',
        ]);
    }
}
