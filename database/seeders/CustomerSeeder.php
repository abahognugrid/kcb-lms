<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory()->create([
            'First_Name' => "Abaho",
            'Last_Name' => "Allan",
            'Telephone_Number' => '256700460055',
            'Gender' => 'Male',
        ]);
    }
}
