<?php

namespace App\Livewire\FinancialReports;

use App\Actions\FinancialReports\GetBalanceSheetReportDetailsAction;
use App\Exports\BalanceSheetExport;
use App\Services\PdfGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class BalanceSheet extends Component
{
    use WithPagination;

    public string $endDate = '';
    public string $startDate = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.reports.balance-sheet', $this->getViewData());
    }

    public function printReport()
    {
        // Format data for the report
        $filters = $this->getFilters();
        $filters['endDate'] = Carbon::parse($this->endDate)->format('d-m-Y');
        $filters['startDate'] = Carbon::parse($this->startDate)->format('d-m-Y');

        // Get data to pass to the report
        $viewData = $this->getViewData();
        $viewData['filters'] = $filters;
        $viewData['partner'] = auth()->user()->partner;

        return app(PdfGeneratorService::class)
            ->view('pdf.financial-position', $viewData)
            ->streamFromLivewire();
    }

    public function excelExport(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(
            new BalanceSheetExport($this->getFilters()),
            'financial-position-' . $this->endDate . '.xlsx'
        );
    }

    private function getReportData()
    {
        return app(GetBalanceSheetReportDetailsAction::class)
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
