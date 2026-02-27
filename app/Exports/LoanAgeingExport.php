<?php

namespace App\Exports;

use App\Actions\Reports\GetLoanAgeingReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class LoanAgeingExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.loan-ageing', [
            'records' => app(GetLoanAgeingReportDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Loan Ageing';
    }
}
