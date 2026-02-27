<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\ExportCbaReportAction;
use App\Actions\Reports\GetDisbursementReportDetailsAction;
use App\Exports\DisbursementExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use League\Csv\InvalidArgument;
use Livewire\Component;
use Livewire\WithPagination;

class DisbursementReport extends Component
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
        return view('livewire.reports.disbursement-report', [
            'records' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.loan-disbursements');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(DisbursementExport::class);
    }

    /**
     * @throws InvalidArgument
     */
    public function exportCba()
    {
        // todo: Export this in the background
        $path = app(ExportCbaReportAction::class)->execute();

        if (empty($path)) {
            return redirect()
                ->to('/reports/loans/disbursement')
                ->with('error', 'There was an error exporting the CBA file. Please try again.');
        }

        return response()->download(storage_path('app/' . $path));
    }

    private function getReportData()
    {
        return app(GetDisbursementReportDetailsAction::class)
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

    public function getFilename(): string
    {
        return str(self::class)->afterLast('\\')->snake()->toString() . now()->toDateString() . '.xlsx';
    }
}
