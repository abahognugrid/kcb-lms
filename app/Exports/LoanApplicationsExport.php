<?php

namespace App\Exports;

use App\Models\Partner;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Database\Eloquent\Collection;
use App\Actions\Reports\GetDisbursementReportDetailsAction;
use App\Actions\Reports\GetLoanApplicationReportDetailsAction;
use App\Actions\OtherReports\GetTransactionsReportDetailsAction;

class LoanApplicationsExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    /**
     * @return Collection
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetLoanApplicationReportDetailsAction::class)->filters($this->filters)->execute();

        return view('excel.loan-applications-report', [
            'records' => $records,
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Loan Applications';
    }
}
