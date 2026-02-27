<?php

namespace App\Exports;

use App\Actions\OtherReports\GetCreditLimitsReportDetailsAction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class CreditLimitsExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    public function view(): View
    {
        return view('excel.credit-limits-report', [
            'records' => app(GetCreditLimitsReportDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Credit Limits';
    }
}
