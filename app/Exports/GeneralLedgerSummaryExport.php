<?php

namespace App\Exports;

use App\Actions\OtherReports\GetGeneralLedgerBreakdownDetailsAction;
use App\Actions\OtherReports\GetGeneralLedgerSummaryDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class GeneralLedgerSummaryExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.general-ledger-summary', [
            'records' => app(GetGeneralLedgerSummaryDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'General Ledger Summary';
    }
}
