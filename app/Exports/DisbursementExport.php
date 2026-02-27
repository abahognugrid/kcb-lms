<?php

namespace App\Exports;

use App\Actions\OtherReports\GetTransactionsReportDetailsAction;
use App\Actions\Reports\GetDisbursementReportDetailsAction;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DisbursementExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    /**
     * @return Collection
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetDisbursementReportDetailsAction::class)->filters($this->filters)->execute();

        return view('excel.disbursement-report', [
            'records' => $records,
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Disbursements';
    }
}
