<?php

namespace Database\Seeders;

use App\Models\SmsLog;
use Illuminate\Database\Seeder;

class SmsLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SmsLog::factory(20)->create();
    }
}
