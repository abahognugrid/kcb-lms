<?php

namespace App\Actions\Loans;

use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class GetJournalBalancesAction
{
    public function execute(EloquentCollection|Collection $records, int $accountId, string $startDate): array
    {
        if ($records->isEmpty()) {
            // We don't have journal entries.
            return [
                'opening_balance' => 0,
                'closing_balance' => 0,
            ];
        }

        $firstRecord = $records->first();

        $balancesBeforeStartDate = JournalEntry::query()
            ->where('account_id', $accountId)
            ->whereDate('created_at', '<', $startDate)
            ->selectRaw('SUM(debit_amount) as debit_balance, SUM(credit_amount) as credit_balance')
            ->first();

        if (in_array($firstRecord->account->type_letter, ['A', 'E'])) {
            $openingBalance = $balancesBeforeStartDate->debit_balance - $balancesBeforeStartDate->credit_balance;

            return [
                'opening_balance' => $openingBalance,
                'closing_balance' => $openingBalance + $records->sum('debit_amount') - $records->sum('credit_amount'),
            ];
        }

        $openingBalance = $balancesBeforeStartDate->credit_balance - $balancesBeforeStartDate->debit_balance;

        return [
            'opening_balance' => $openingBalance,
            'closing_balance' => ($openingBalance + ($records->sum('credit_amount')) - $records->sum('debit_amount')),
        ];
    }
}
