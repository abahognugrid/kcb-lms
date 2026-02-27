<?php

namespace App\Livewire\OtherReports;

use App\Actions\OtherReports\GetTransactionsReportDetailsAction;
use App\Exports\TransactionsExport;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionReport extends Component
{
    use ExportsData, WithPagination;

    public string $transactionStatus = '';

    public function mount(): void
    {
        $this->startDate = now()->subDays(7)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.transactions-report', [
            'records' => $this->getReportData(),
        ]);
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(TransactionsExport::class);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.transactions-report');
    }

    private function getReportData()
    {
        return app(GetTransactionsReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'transactionStatus' => $this->transactionStatus,
        ];
    }
}
