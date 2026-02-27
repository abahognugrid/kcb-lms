<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetOutstandingLoanReportDetailsAction;
use App\Exports\OutstandingLoanExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class OutstandingLoanReport extends Component
{
    use ExportsData, WithPagination;

    public bool $includeWrittenOffLoans = false;

    public ?int $loanProductId = null;

    public function mount()
    {
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.outstanding-loans-report', [
            'records' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.outstanding-loans');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(OutstandingLoanExport::class);
    }

    private function getReportData()
    {
        return app(GetOutstandingLoanReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'loanProductId' => $this->loanProductId,
            'includeWrittenOffLoans' => $this->includeWrittenOffLoans,
        ];
    }
}
