<?php

namespace App\Actions;

use App\Models\Accounts\Account;
use App\Models\Partner;
use App\Services\Account\AccountSeederService;
use Illuminate\Support\Facades\Log;

class CreateProvisionAccountsAction
{
    public function execute(Partner $partner): bool
    {
        $seeder = new AccountSeederService($partner->id);
        $accounts = [
            $seeder::LOAN_LOSS_PROVISION_SLUG => [
                'name' => $seeder::LOAN_LOSS_PROVISION_NAME,
                'identifier' => $seeder::LOAN_LOSS_PROVISION_IDENTIFIER,
                'class' => null,
                'parent-slug' => $seeder::LIABILITIES_SLUG,
            ],
            $seeder::RECOVERIES_FROM_WRITTEN_OFF_LOANS_SLUG => [
                'name' => $seeder::RECOVERIES_FROM_WRITTEN_OFF_LOANS_NAME,
                'identifier' => $seeder::RECOVERIES_FROM_WRITTEN_OFF_LOANS_IDENTIFIER,
                'class' => null,
                'parent-slug' => $seeder::INCOME_SLUG,
            ],
        ];

        foreach ($accounts as $slug => $record) {
            $parent = Account::query()->where('slug', $record['parent-slug'])->where('partner_id', $partner->id)->first();

            if (empty($parent)) {
                Log::error("Parent account not found for {$record['name']}");

                return false;
            }

            $account = new Account();
            $account->partner_id = $partner->id;
            $account->slug = $slug;
            $account->position = -1;
            $account->identifier = $record['identifier'];
            $account->name = $record['name'];
            $account->type_letter = $parent->type_letter;
            $account->is_fixed = true;
            $account->save();

            $parent->addFixedAccount($account, $record['identifier']);
        }

        return true;
    }
}
