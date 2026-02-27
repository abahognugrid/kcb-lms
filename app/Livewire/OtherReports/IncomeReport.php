<?php

namespace App\Livewire\OtherReports;

use App\Actions\OtherReports\GetIncomeReportDetailsAction;
use App\Exports\IncomeReportExport;
use App\Models\LoanProduct;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class IncomeReport extends Component
{
    use WithPagination, ExportsData;

    public ?int $loanProductId = null;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.reports.income-report', $this->getViewData());
    }

    public function printReport()
    {
        // Get data to pass to the report
        $viewData = $this->getViewData();
        $viewData['filters'] = $this->getFormattedDateFilters();
        $viewData['partnerName'] = auth()->user()?->partner->Institution_Name;

        return app(PdfGeneratorService::class)
            ->view('pdf.income-report', $viewData)
            ->streamFromLivewire();
    }

    public function excelExport(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new IncomeReportExport($this->getFilters()), $this->getExcelFilename());
    }

    private function getReportData()
    {
        return app(GetIncomeReportDetailsAction::class)
            ->filters($this->getFilters())
            ->execute();
    }

    protected function getViewData(): array
    {
        $records = $this->getReportData();

        return [
            'records' => $records,
            'loanProducts' => LoanProduct::query()->pluck('Name', 'id'),
        ];
    }

    public function getFilters(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'loanProductId' => $this->loanProductId,
        ];
    }
}
