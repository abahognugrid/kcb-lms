<?php

namespace App\Exports;

use App\Actions\FinancialReports\GetBalanceSheetReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class BalanceSheetExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.financial-position', [
            'records' => app(GetBalanceSheetReportDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Trial Balance';
    }
}
