<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetExternalAccountsReportDetailsAction;
use App\Exports\ExternalAccountsExport;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ExternalAccountsReport extends Component
{
    use ExportsData, WithPagination;

    public ?string $serviceProvider = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render(): View
    {
        return view('livewire.reports.external-accounts-report', [
            'records' => $this->getReportData(),
            'serviceProviders' => ['Airtel'], // Can be extended in future
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.external-accounts-report');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(ExternalAccountsExport::class);
    }

    private function getReportData()
    {
        return app(GetExternalAccountsReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'serviceProvider' => $this->serviceProvider,
        ];
    }
}
