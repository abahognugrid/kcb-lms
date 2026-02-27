<?php

namespace App\Exports;

use App\Actions\Reports\GetDueLoanReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DueLoansExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.due-loans', [
            'records' => app(GetDueLoanReportDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Due Loans';
    }
}
