<?php

namespace App\Actions\FinancialReports;

use App\Models\JournalEntry;
use App\Services\Account\AccountSeederService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetIncomeStatementReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    public function execute(): object
    {
        $partnerId = auth()->user()->partner_id;
        $carbonEndDate = Carbon::parse($this->endDate)->endOfDay()->toDateTimeString();
        $carbonStartDate = Carbon::parse($this->startDate)->startOfDay()->toDateTimeString();

        $results = JournalEntry::query()
            ->select(
                'account_id',
                'accounts.type_letter',
                DB::raw('SUM(credit_amount) as total_credit'),
                DB::raw('SUM(debit_amount) as total_debit')
            )
            ->join('accounts', 'accounts.id', '=', 'journal_entries.account_id')
            ->where('journal_entries.partner_id', $partnerId)
            ->whereBetween('journal_entries.created_at', [
                $carbonStartDate,
                $carbonEndDate
            ])
            ->whereIn('accounts.type_letter', ['I', 'E'])
            ->groupBy('account_id', 'accounts.type_letter')
            ->with('account')
            ->afterQuery(function ($accounts) {
                foreach ($accounts as $account) {
                    $account->balance = 0;

                    if ($account->type_letter === 'I') {
                        $account->balance = $account->total_credit - $account->total_debit;
                    } else if ($account->type_letter === 'E') {
                        $account->balance = $account->total_debit - $account->total_credit;
                    }
                }
            })
            ->get();

        $revenues = $results->where('type_letter', 'I');
        $expenses = $results->where('type_letter', 'E');

        // Calculate Net Income
        $total_revenue = $revenues->sum('balance');
        $total_expenses = $expenses->sum('balance');
        $net_income = $total_revenue - $total_expenses;

        return (object) [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => $total_revenue,
            'total_expenses' => $total_expenses,
            'net_income' => $net_income,
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
}
