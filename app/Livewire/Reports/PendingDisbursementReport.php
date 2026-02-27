<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetPendingDisbursementReportDetailsAction;
use App\Exports\PendingDisbursementExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;

class PendingDisbursementReport extends Component
{
    use ExportsData, WithPagination;

    public ?int $loanProductId = null;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.pending-disbursement-report', [
            'records' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.pending-disbursements');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(PendingDisbursementExport::class);
    }

    private function getReportData()
    {
        return app(GetPendingDisbursementReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    public function addFilters(): array
    {
        return [
            'loanProductId' => $this->loanProductId,
        ];
    }
}
