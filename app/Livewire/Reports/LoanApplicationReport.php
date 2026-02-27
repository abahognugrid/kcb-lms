<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\ExportCapReportAction;
use App\Actions\Reports\GetLoanApplicationReportDetailsAction;
use App\Exports\LoanApplicationsExport;
use App\Models\LoanProduct;
use App\Traits\ExportsData;
use League\Csv\InvalidArgument;
use Livewire\Component;
use Livewire\WithPagination;

class LoanApplicationReport extends Component
{
    use ExportsData, WithPagination;

    public ?int $loanProductId = null;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.loan-application-report', [
            'records' => $this->getReportData(),
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.loan-applications');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(LoanApplicationsExport::class);
    }

    /**
     * @throws InvalidArgument
     */
    public function exportCap()
    {
        $path = app(ExportCapReportAction::class)->execute();

        if (empty($path)) {
            return redirect()
                ->to('/reports/loan-applications')
                ->with('error', 'There was an error exporting the CAP file. Please try again.');
        }

        return response()->download(storage_path('app/' . $path));
    }

    private function getReportData()
    {
        return app(GetLoanApplicationReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    public function addFilters(): array
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
