<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetRepaymentReportDetailsAction;
use App\Exports\RepaymentExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RepaymentReport extends Component
{
    use ExportsData, WithPagination;

    public ?int $loanProductId = null;

    public function mount(): void
    {
        $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.repayment-report', [
            'records' => $this->getRecords(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.loan-repayments');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(RepaymentExport::class);
    }

    private function getRecords()
    {
        return app(GetRepaymentReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'loanProductId' => $this->loanProductId,
        ];
    }
}
