<?php

namespace App\Livewire\OtherReports;

use App\Actions\Reports\GetPerformanceMetricsReportDetailsAction;
use App\Exports\PerformanceMetricsExport;
use App\Services\Account\AccountSeederService;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;

class PerformanceMetricsReport extends Component
{
    use WithPagination, ExportsData;

    public string $startDate = '';
    public string $endDate = '';
    public string $accountType = AccountSeederService::COLLECTION_OVA_SLUG;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render(): View
    {
        return view('livewire.reports.performance-metrics', $this->getViewData());
    }

    public function printReport()
    {
        // Get data to pass to the report
        $viewData = $this->getViewData();
        $viewData['filters'] = $this->getFormattedDateFilters();
        $viewData['partnerName'] = auth()->user()?->partner->Institution_Name;
        $viewData['partner'] = auth()->user()->partner;

        return app(PdfGeneratorService::class)
            ->view('pdf.performance-metrics', $viewData)
            ->streamFromLivewire();
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excelExport(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new PerformanceMetricsExport($this->getFilters()), 'performance-metrics-report-'.now()->toDateString().'.xlsx');
    }

    private function getReportData()
    {
        return app(GetPerformanceMetricsReportDetailsAction::class)
            ->filters($this->getFilters())
            ->execute();
    }

    protected function getViewData(): array
    {
        $records = $this->getReportData();

        return [
            'records' => $records
        ];
    }
}
