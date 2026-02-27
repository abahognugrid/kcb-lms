<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\BankAccount;
use App\Services\Account\AccountSeederService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Partner::all() as $partner) {
            BankAccount::create([
                'partner_id' => $partner->id,
                'name' => AccountSeederService::LOAN_OVA_ESCROW_BANK_ACCOUNT_NAME,
            ]);
        }
    }
}
