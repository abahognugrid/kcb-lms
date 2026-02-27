<?php

namespace App\Exports;

use App\Actions\OtherReports\GetTransactionsReportDetailsAction;
use App\Actions\Reports\GetPerformanceMetricsReportDetailsAction;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PerformanceMetricsExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    /**
     * @return Collection
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetPerformanceMetricsReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();

        return view('excel.performance-metrics', [
            'records' => $records,
            'partner' => auth()->user()->partner,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Performance Metrics';
    }
}
