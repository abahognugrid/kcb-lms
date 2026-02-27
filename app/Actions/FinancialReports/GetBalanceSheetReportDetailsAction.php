<?php

namespace App\Actions\FinancialReports;

use App\Models\JournalEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetBalanceSheetReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    public function execute(): object
    {
        $partnerId = auth()->user()->partner_id;
        $carbonEndDate = Carbon::parse($this->endDate)->endOfDay()->toDateTimeString();

        $records = DB::table('accounts as a')
            ->join('journal_entries as je', 'je.account_id', '=', 'a.id')
            ->selectRaw('a.id, a.name, a.type_letter, a.identifier, sum(je.debit_amount) as total_debit, sum(je.credit_amount) as total_credit')
            ->where('je.partner_id', $partnerId)
            ->whereDate('je.created_at', '<=', $carbonEndDate)
            ->where('a.partner_id', $partnerId)
            ->whereIn('a.type_letter', ['A', 'L', 'C', 'I'])
            ->addSelect([
                'opening_balance' => JournalEntry::query()
                    ->select('previous_balance')
                    ->where('partner_id', $partnerId)
                    ->whereColumn('account_id', 'a.id')
                    ->whereDate('created_at', '<=', $carbonEndDate)
                    ->oldest()
                    ->limit(1),
                'closing_balance' => JournalEntry::query()
                    ->select('current_balance')
                    ->where('partner_id', $partnerId)
                    ->whereColumn('account_id', 'a.id')
                    ->whereDate('created_at', '<=', $carbonEndDate)
                    ->latest()
                    ->limit(1),
            ])
            ->groupBy(['a.id', 'a.name', 'a.identifier', 'a.type_letter'])
            ->orderBy('a.name')
            ->afterQuery(function ($accounts) {
                foreach ($accounts as $account) {
                    if (in_array($account->type_letter, ['A', 'E'])) {
                        $account->balance = $account->total_debit - $account->total_credit;
                    } elseif (in_array($account->type_letter, ['C', 'I', 'L'])) {
                        $account->balance = $account->total_credit - $account->total_debit;
                    }
                }
            })
            ->get()
            ->groupBy('type_letter');

        $emptyCollection = collect();

        return (object) [
            'assets' => $records->get('A', $emptyCollection),
            'capital' => $records->get('C', $emptyCollection),
            'liabilities' => $records->get('L', $emptyCollection),
            'income' => $records->get('I', $emptyCollection),
            'retainedEarnings' => $this->getRetainedEarnings(),
        ];
    }

    public function filters(array $details): self
    {
        $this->startDate = Arr::get($details, 'startDate', now()->startOfMonth()->toDateString());
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        if (Carbon::parse($this->startDate)->isAfter($this->endDate)) {
            $this->startDate = $this->endDate;
        }

        return $this;
    }

    protected function getRetainedEarnings(): float
    {
        $results = JournalEntry::query()
            ->select(
                'account_id',
                'accounts.type_letter',
                DB::raw('SUM(credit_amount) as total_credit'),
                DB::raw('SUM(debit_amount) as total_debit')
            )
            ->join('accounts', 'accounts.id', '=', 'journal_entries.account_id')
            ->where('journal_entries.partner_id', auth()->user()->partner_id)
            ->whereDate('journal_entries.created_at', '<', Carbon::parse($this->endDate)->endOfDay()->toDateTimeString())
            ->whereIn('accounts.type_letter', ['I', 'E'])
            ->groupBy('account_id', 'accounts.type_letter')
            ->with('account')
            ->get();

        $revenues = $results->where('type_letter', 'I');
        $expenses = $results->where('type_letter', 'E');

        // Calculate Net Income
        $total_revenue = $revenues->sum('total_credit') - $revenues->sum('total_debit');
        $total_expenses = $expenses->sum('total_debit') - $expenses->sum('total_credit');
        return $total_revenue - $total_expenses;
    }
}
