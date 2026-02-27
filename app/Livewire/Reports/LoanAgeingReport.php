<?php

namespace App\Livewire\Reports;

use App\Actions\Loans\GetAgeingDaysAction;
use App\Actions\Reports\GetLoanAgeingReportDetailsAction;
use App\Exports\LoanAgeingExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;

class LoanAgeingReport extends Component
{
    use ExportsData, WithPagination;

    public bool $excludeNotDue = false;

    public ?int $loanProductId = null;

    public function mount()
    {
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.loan-ageing-report', [
            'loans' => $this->getReportData(),
            'ageingDays' => app(GetAgeingDaysAction::class)->execute(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.loan-ageing');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(LoanAgeingExport::class);
    }

    private function getReportData()
    {
        return app(GetLoanAgeingReportDetailsAction::class)
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
}
