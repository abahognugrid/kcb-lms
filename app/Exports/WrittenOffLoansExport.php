<?php

namespace App\Exports;

use App\Actions\Reports\GetWrittenOffLoansReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class WrittenOffLoansExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetWrittenOffLoansReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();

        return view('excel.written-off-loans', [
            'records' => $records,
            'filters' => $this->filters,
            'showRecoveries' => $this->filters['showRecoveries'] ?? false,
        ]);
    }

    public function title(): string
    {
        return 'Written Off Loans';
    }
}
