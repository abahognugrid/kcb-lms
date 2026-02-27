<?php

namespace App\Livewire\OtherReports;

use App\Actions\OtherReports\GetGeneralLedgerSummaryDetailsAction;
use App\Exports\GeneralLedgerSummaryExport;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GeneralLedgerSummaryReport extends Component
{
    use WithPagination, ExportsData;

    public string $endDate = '';
    public string $startDate = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.reports.general-ledger-summary', $this->getViewData());
    }

    public function printReport()
    {
        return app(PdfGeneratorService::class)
            ->view('pdf.general-ledger-summary', [
                'records' => $this->getReportData(),
                'partnerName' => auth()->user()?->partner->Institution_Name,
                'filters' => $this->getFilters(),
            ])
            ->streamFromLivewire();
    }

    public function excelExport(): BinaryFileResponse
    {
        return Excel::download(new GeneralLedgerSummaryExport($this->getFilters()), $this->getExcelFilename());
    }

    private function getReportData()
    {
        return app(GetGeneralLedgerSummaryDetailsAction::class)
            ->filters($this->getFilters())
            ->execute();
    }

    protected function getFilters(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }

    protected function getViewData(): array
    {
        $records = $this->getReportData();

        return [
            'records' => $records
        ];
    }
}
