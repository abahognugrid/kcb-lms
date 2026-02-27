<?php

namespace App\Livewire\OtherReports;

use App\Actions\OtherReports\GetSmsReportDetailsAction;
use App\Exports\SmsExport;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class SmsReport extends Component
{
    use ExportsData, WithPagination;

    public function mount(): void
    {
        $this->startDate = now()->subDays(7)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.sms-report', [
            'records' => $this->getReportData()
        ]);
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(SmsExport::class);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.sms-report');
    }

    private function getReportData()
    {
        return app(GetSmsReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }
}
