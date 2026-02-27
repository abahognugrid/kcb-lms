<?php

namespace App\Exports;

use App\Actions\Loans\GetAgeingDaysAction;
use App\Actions\Reports\GetPortfolioAtRiskReportDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PortfolioAtRiskExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.portfolio-at-risk', [
            'loans' => app(GetPortfolioAtRiskReportDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Portfolio At Risk';
    }
}
