<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetDueLoanReportDetailsAction;
use App\Exports\DueLoansExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;

class DueLoansReport extends Component
{
    use ExportsData, WithPagination;

    public ?int $loanProductId = null;

    public function mount()
    {
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.due-loans-report', [
            'records' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.due-loans');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(DueLoansExport::class);
    }

    private function getReportData(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return app(GetDueLoanReportDetailsAction::class)
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
