<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetWrittenOffLoansReportDetailsAction;
use App\Exports\WrittenOffLoansExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;

class WrittenOffLoansReport extends Component
{
    use ExportsData, WithPagination;

    public bool $showRecoveries = false;

    public ?int $loanProductId = null;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.written-off-loans-report', [
            'records' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.written-off-loans');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(WrittenOffLoansExport::class);
    }

    private function getReportData()
    {
        $action = app(GetWrittenOffLoansReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters());

        if ($this->showRecoveries) {
            $action->withRecovery();
        }

        return $action->execute();
    }

    protected function addFilters(): array
    {
        return [
            'loanProductId' => $this->loanProductId,
            'showRecoveries' => $this->showRecoveries,
        ];
    }
}
