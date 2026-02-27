<?php

namespace App\Exports;

use App\Actions\Loans\GetJournalBalancesAction;
use App\Actions\OtherReports\GetGeneralLedgerBreakdownDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class GeneralLedgerBreakdownExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetGeneralLedgerBreakdownDetailsAction::class)
            ->filters($this->filters)
            ->execute();
        return view('excel.general-ledger-breakdown', [
            'records' => $records,
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
            'summary' => app(GetJournalBalancesAction::class)
                ->execute(
                    $records,
                    data_get($this->filters, 'accountId'),
                    data_get($this->filters, 'startDate')
                ),
        ]);
    }

    public function title(): string
    {
        return 'General Ledger Breakdown';
    }
}
