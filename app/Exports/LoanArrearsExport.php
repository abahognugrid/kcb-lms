<?php

namespace App\Exports;

use App\Actions\Reports\GetLoanArrearsReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class LoanArrearsExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetLoanArrearsReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();

        return view('excel.loan-arrears', [
            'records' => $records,
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Loan Arrears';
    }
}