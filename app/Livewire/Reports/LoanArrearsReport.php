<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetLoanArrearsReportDetailsAction;
use App\Exports\LoanArrearsExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;

class LoanArrearsReport extends Component
{
    use ExportsData, WithPagination;

    public bool $suspendedInterest = false;

    public bool $excludeWrittenOffLoans = false;

    public ?int $loanProductId = null;

    public function mount(): void
    {
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.loan-arrears-report', [
            'records' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.loan-arrears');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(LoanArrearsExport::class);
    }

    private function getReportData(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return app(GetLoanArrearsReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'suspendedInterest' => $this->suspendedInterest,
            'excludeWrittenOffLoans' => $this->excludeWrittenOffLoans,
            'loanProductId' => $this->loanProductId,
        ];
    }
}
