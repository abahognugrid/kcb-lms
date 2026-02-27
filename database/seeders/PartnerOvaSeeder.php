<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\PartnerOva;
use Illuminate\Database\Seeder;

class PartnerOvaSeeder extends Seeder
{
    public function run(): void
    {
        PartnerOva::create([
            'partner_id' => Partner::first()->id,
            'app_name' => 'KCBBANK',
            'airtel_url' => '',
            'client_key' => 'KCBLOANSB',
            'client_secret' => 'Kcb@2024',
            'pin' => '',
            'airtel_public_key' => '',
            'airtel_callback' => '',
        ]);
    }
}
