<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetCashFlowDetailsAction;
use App\Exports\CashFlowExport;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;

class CashFlowReport extends Component
{
    use ExportsData, WithPagination;

    public function mount()
    {
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.cash-flow-report', $this->getViewData());
    }

    public function updatedEndDate($value)
    {
        if (! validate_date($value, 'Y-m-d')) {
            $this->endDate = now()->format('Y-m-d');
        }
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.cash-flow-statement');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(CashFlowExport::class);
    }

    private function getReportData()
    {
        return app(GetCashFlowDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'endDate' => $this->endDate,
        ];
    }

    protected function getViewData(): array
    {
        return [
            'journalEntries' => $this->getReportData(),
        ];
    }
}
