<?php

namespace Database\Seeders;

use App\Models\CollectionOVA;
use App\Models\DisbursementOVA;
use App\Models\Partner;
use App\Services\Account\AccountSeederService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OVAAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Partner::all() as $partner) {
            CollectionOVA::create([
                'partner_id' => $partner->id,
                'name' => AccountSeederService::COLLECTION_OVA_NAME
            ]);
            DisbursementOVA::create([
                'partner_id' => $partner->id,
                'name' => AccountSeederService::DISBURSEMENT_OVA_NAME
            ]);
        }
    }
}
