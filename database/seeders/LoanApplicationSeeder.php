<?php

namespace Database\Seeders;

use App\Models\LoanApplication;
use Illuminate\Database\Seeder;

class LoanApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed 50 loan applications
        LoanApplication::factory()->count(50)->create();
    }
}