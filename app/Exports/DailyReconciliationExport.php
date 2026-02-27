<?php

namespace App\Exports;

use App\Actions\OtherReports\GetDailyReconciliationReportDetailsAction;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DailyReconciliationExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetDailyReconciliationReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();

        $currentTotal = $records->sum('total_amount');

        return view('excel.daily-reconciliation', [
            'records' => $records,
            'openingRecord' => $records->first(),
            'closingRecord' => $records->last(),
            'partnerName' => Auth::user()?->partner->Institution_Name,
            'filters' => $this->filters,
            'summary' => $this->calculateSummary($currentTotal),
        ]);
    }

    public function title(): string
    {
        return 'Daily Reconciliation';
    }

    protected function calculateSummary($currentTotal): array
    {
        $partnerId = Auth::user()->partner_id;

        // Get opening balance for the specific loan product
        $balanceQuery = JournalEntry::query()
            ->join('transactions as t', 'journal_entries.transaction_id', '=', 't.id')
            ->join('loan_applications as la', 't.Loan_Application_ID', '=', 'la.id')
            ->join('accounts as a', 'journal_entries.account_id', '=', 'a.id')
            ->when(data_get($this->filters, 'accountType'), function ($query) {
                $query->where('a.slug', data_get($this->filters, 'accountType'));
            })
            ->when(data_get($this->filters, 'loanProductId'), function ($query) {
                $query->where('la.Loan_Product_ID', data_get($this->filters, 'loanProductId'));
            })
            ->when($partnerId, function ($query) use ($partnerId) {
                $query->where('journal_entries.partner_id', $partnerId);
            })
            ->whereDate('journal_entries.created_at', '<', data_get($this->filters, 'startDate'))
            ->selectRaw('COALESCE(SUM(journal_entries.debit_amount), 0) as debit_balance, COALESCE(SUM(journal_entries.credit_amount), 0) as credit_balance')
            ->first();

        if (data_get($this->filters, 'isDisbursement')) {
            $openingBalance = $balanceQuery->credit_balance;
        } else {
            $openingBalance = $balanceQuery->debit_balance;
        }

        return [
            'opening_balance' => $openingBalance,
            'closing_balance' => $openingBalance + $currentTotal,
        ];
    }
}
