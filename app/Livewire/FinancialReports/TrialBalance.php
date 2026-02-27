<?php

namespace App\Livewire\FinancialReports;

use App\Actions\FinancialReports\GetTrialBalanceReportDetailsAction;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrialBalanceExport;

class TrialBalance extends Component
{
    use WithPagination, ExportsData;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.reports.trial-balance', $this->getViewData());
    }

    public function printReport()
    {
        // Get data to pass to the report
        $viewData = $this->getViewData();
        $viewData['filters'] = $this->getFormattedDateFilters();
        $viewData['partnerName'] = auth()->user()?->partner->Institution_Name;

        return app(PdfGeneratorService::class)
            ->view('pdf.trial-balance', $viewData)
            ->streamFromLivewire();
    }

    public function excelExport()
    {
        return Excel::download(
            new TrialBalanceExport($this->getFilters()),
            'trial-balance-' . $this->endDate . '.xlsx'
        );
    }

    private function getReportData()
    {
        return app(GetTrialBalanceReportDetailsAction::class)
            ->filters($this->getFilters())
            ->execute();
    }

    protected function getFilters(): array
    {
        return [
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
