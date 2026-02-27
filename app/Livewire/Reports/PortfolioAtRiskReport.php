<?php

namespace App\Livewire\Reports;

use App\Actions\Loans\GetAgeingDaysAction;
use App\Actions\Reports\GetPortfolioAtRiskReportDetailsAction;
use App\Exports\PortfolioAtRiskExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class PortfolioAtRiskReport extends Component
{
    use ExportsData, WithPagination;

    public bool $excludeNotDue = false;

    public ?int $loanProductId = null;

    public function mount(): void
    {
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.portfolio-at-risk-report', [
            'loans' => $this->getReportData(),
            'ageingDays' => app(GetAgeingDaysAction::class)->execute(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.portfolio-at-risk');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(PortfolioAtRiskExport::class);
    }

    private function getReportData()
    {
        return app(GetPortfolioAtRiskReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'excludeNotDue' => $this->excludeNotDue,
            'loanProductId' => $this->loanProductId,
            'ageingDays' => app(GetAgeingDaysAction::class)->execute(),
        ];
    }

    public function getFormattedFilters(): array
    {
        return [
            'endDate' => Carbon::parse($this->endDate)->format('d-m-Y'),
            'excludeNotDue' => $this->excludeNotDue,
        ];
    }
}
