<?php

namespace App\Exports;

use App\Actions\Reports\GetDelinkedCustomerReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DelinkedCustomersExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetDelinkedCustomerReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();

        return view('excel.delinked-customers', [
            'records' => $records,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Delinked Customers';
    }
}
