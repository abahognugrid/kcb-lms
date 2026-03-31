<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetDelinkedCustomerReportDetailsAction;
use App\Exports\DelinkedCustomersExport;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class DelinkedCustomersReport extends Component
{
    use ExportsData, WithPagination;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.delinked-customers-report', [
            'records' => $this->getReportData(),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.delinked-customers');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(DelinkedCustomersExport::class);
    }

    private function getReportData()
    {
        return app(GetDelinkedCustomerReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }
}
