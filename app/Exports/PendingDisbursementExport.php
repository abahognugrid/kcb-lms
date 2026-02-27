<?php

namespace App\Exports;

use App\Actions\Reports\GetPendingDisbursementReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PendingDisbursementExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetPendingDisbursementReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();

        return view('excel.pending-disbursements', [
            'records' => $records,
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Pending Disbursements';
    }
}