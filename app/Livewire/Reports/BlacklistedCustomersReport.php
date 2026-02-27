<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetBlacklistedCustomerReportDetailsAction;
use App\Exports\BlackListedCustomersExport;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BlacklistedCustomersReport extends Component
{
    use ExportsData, WithPagination;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.blacklisted-customer-report', [
            'records' => $this->getReportData(),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.blacklisted-customers');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(BlackListedCustomersExport::class);
    }

    private function getReportData()
    {
        return app(GetBlacklistedCustomerReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }
}
