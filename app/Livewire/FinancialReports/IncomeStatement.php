<?php

namespace App\Livewire\FinancialReports;

use App\Actions\FinancialReports\GetBalanceSheetReportDetailsAction;
use App\Actions\FinancialReports\GetIncomeStatementReportDetailsAction;
use App\Exports\IncomeStatementExport;
use App\Services\PdfGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class IncomeStatement extends Component
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
        return view('livewire.reports.income-statement', $this->getViewData());
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
        $viewData['partnerName'] = auth()->user()?->partner->Institution_Name;

        return app(PdfGeneratorService::class)
            ->view('pdf.income-statement', $viewData)
            ->streamFromLivewire();
    }

    public function excelExport(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(
            new IncomeStatementExport($this->getFilters()),
            'income-statement-' . $this->endDate . '.xlsx'
        );
    }

    private function getReportData()
    {
        return app(GetIncomeStatementReportDetailsAction::class)
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
        return (array) $this->getReportData();
    }
}
