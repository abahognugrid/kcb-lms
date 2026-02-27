<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\PartnerApiSetting;
use Illuminate\Database\Seeder;

class PartnerApiSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerApiSetting::create([
            'partner_id' => Partner::first()->id,
            'api_key' => str()->random(32),
        ]);
    }
}
