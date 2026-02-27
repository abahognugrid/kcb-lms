<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\SavingsProduct;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SavingsProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Partner::all() as $partner) {
            SavingsProduct::create([
                'partner_id' => $partner->id,
                'name' => ' Savings Product',
                'description' => 'Basic savings account',
                'code' => 'SP001',
                'cost' => 0.00,
            ]);
        }
    }
}
