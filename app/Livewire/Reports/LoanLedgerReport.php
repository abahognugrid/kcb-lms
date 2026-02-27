<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetLoanLedgerReportDetailsAction;
use App\Exports\LoanLedgerExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class LoanLedgerReport extends Component
{
    use ExportsData, WithPagination;

    public ?int $loanProductId = null;

    public ?int $loanId = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.loan-ledger-report', [
            'records' => $this->getReportData()
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.loan-ledger');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(LoanLedgerExport::class);
    }

    private function getReportData()
    {
        $filters = $this->getFilters();

        return app(GetLoanLedgerReportDetailsAction::class)
            ->paginate()
            ->filters($filters)
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'loanProductId' => $this->loanProductId,
            'loanId' => $this->loanId,
        ];
    }
}
