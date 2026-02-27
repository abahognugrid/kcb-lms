<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetPaidOffLoansReportDetailsAction;
use App\Exports\PaidOffLoansExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PaidOffLoansReport extends Component
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
        return view('livewire.reports.paid-off-loans-report', [
            'loans' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.paid-off-loans');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(PaidOffLoansExport::class);
    }

    private function getReportData(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return app(GetPaidOffLoansReportDetailsAction::class)
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
