<?php

namespace App\Traits;

use App\Models\Accounts\Account;
use App\Services\Account\AccountSeederService;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait AccountOperations
{
    protected function addInterestAccount($partner_id)
    {
        try {
            $parent_gla = Account::where('identifier', 'AR')->where('partner_id', $partner_id)->first();
            $account = new Account;
            $account->name = AccountSeederService::INTEREST_RECEIVABLES_NAME;
            $account->partner_id = $parent_gla->partner_id;
            $account->parent_id = $parent_gla->id;
            $account->identifier = $parent_gla->identifier . '.' . rand(100, 999);
            $account->type_letter = $parent_gla->type_letter;
            $account->slug = Str::slug($account->name);
            $account->position = 3;
            $account->save();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    protected function addPenaltiesReceivableAccount($partner_id)
    {
        try {
            $parent_gla = Account::where('identifier', 'AR')->where('partner_id', $partner_id)->first();
            $account = new Account;
            $account->name = AccountSeederService::PENALTIES_RECEIVABLES_NAME;
            $account->partner_id = $parent_gla->partner_id;
            $account->parent_id = $parent_gla->id;
            $account->identifier = $parent_gla->identifier . '.' . rand(100, 999);
            $account->type_letter = $parent_gla->type_letter;
            $account->slug = Str::slug($account->name);
            $account->position = 3;
            $account->save();
            return $account;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
