<?php

namespace App\Exports;

use App\Actions\Reports\GetProvisionsReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProvisionsReportExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetProvisionsReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();

        return view('excel.provisions-report', [
            'records' => $records,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Loan Loss Provisions';
    }
}
