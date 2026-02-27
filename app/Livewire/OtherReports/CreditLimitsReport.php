<?php

namespace App\Livewire\OtherReports;

use App\Actions\OtherReports\GetCreditLimitsReportDetailsAction;
use App\Exports\CreditLimitsExport;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;

class CreditLimitsReport extends Component
{
    use ExportsData, WithPagination;

    public function mount(): void
    {
        $this->startDate = now()->subDays(7)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.credit-limits-report', [
            'records' => $this->getReportData()
        ]);
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(CreditLimitsExport::class);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.credit-limits-report');
    }

    private function getReportData()
    {
        return app(GetCreditLimitsReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }
}
