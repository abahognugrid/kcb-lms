<?php

namespace App\Exports;

use App\Actions\Reports\GetBlacklistedCustomerReportDetailsAction;
use App\Actions\Reports\GetWrittenOffLoansReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class BlackListedCustomersExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetBlacklistedCustomerReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();

        return view('excel.blacklisted-customers', [
            'records' => $records,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Written Off Loans';
    }
}
