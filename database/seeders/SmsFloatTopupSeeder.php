<?php

namespace Database\Seeders;

use App\Models\SmsFloatTopup;
use Illuminate\Database\Seeder;

class SmsFloatTopupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SmsFloatTopup::factory(1)->create();
    }
}
