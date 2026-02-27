<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetProvisionsReportDetailsAction;
use App\Exports\ProvisionsReportExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProvisionsReport extends Component
{
    use ExportsData, WithPagination;

    public ?int $loanProductId = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.provisions-report', [
            'records' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->validate([
            'loanProductId' => 'required',
        ]);

        $this->generatePdfReport('pdf.provisions-report');
    }

    public function excelExport(): void
    {
        $this->validate([
            'loanProductId' => 'required',
        ]);

        $this->generateExcelReport(ProvisionsReportExport::class);
    }

    private function getReportData()
    {
        return app(GetProvisionsReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'loanProductId' => $this->loanProductId,
            'loanProductName' => LoanProduct::query()->find($this->loanProductId)?->Name
        ];
    }
}
