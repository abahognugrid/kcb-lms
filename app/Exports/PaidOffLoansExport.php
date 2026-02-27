<?php

namespace App\Exports;

use App\Actions\Reports\GetPaidOffLoansReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PaidOffLoansExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.paid-off-loans', [
            'loans' => app(GetPaidOffLoansReportDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'partner' => auth()->user()->partner,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Paid Off Loans';
    }
}
