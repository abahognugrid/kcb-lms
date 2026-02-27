<?php

namespace App\Actions\FinancialReports;

use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetTrialBalanceReportDetailsAction
{
    protected string $endDate = '';
    protected int $perPage = 0;
    public function execute(): \Illuminate\Support\Collection
    {
        return JournalEntry::query()
            ->select(
                'account_id',
                DB::raw('SUM(debit_amount) as total_debit'),
                DB::raw('SUM(credit_amount) as total_credit')
            )
            ->where('partner_id', auth()->user()->partner_id)
            ->whereDate('created_at', '<=', Carbon::parse($this->endDate)->toDateString())
            ->groupBy('account_id')
            ->with('account')
            ->afterQuery(function (Collection $journalEntries) {
                $journalEntries->each(function ($journalEntry) {
                    $journalEntry->debit_amount = 0;
                    $journalEntry->credit_amount = 0;

                    if (in_array($journalEntry->account->type_letter, ['A', 'E'])) {
                        $journalEntry->debit_amount = $journalEntry->total_debit - $journalEntry->total_credit;
                    } elseif (in_array($journalEntry->account->type_letter, ['C', 'I', 'L'])) {
                        $journalEntry->credit_amount = $journalEntry->total_credit - $journalEntry->total_debit;
                    }
                });
            })
            ->get();
    }

    public function filters(array $details): self
    {
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        return $this;
    }
}
