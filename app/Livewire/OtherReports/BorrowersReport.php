<?php

namespace App\Livewire\OtherReports;

use App\Actions\OtherReports\GetBorrowersReportDetailsAction;
use App\Exports\BorrowersExport;
use App\Services\PdfGeneratorService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class BorrowersReport extends Component
{
    public string $startDate = '';
    public string $endDate = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.borrowers-report', [
            'records' => $this->getReportData()
        ]);
    }

    public function printReport()
    {
        return app(PdfGeneratorService::class)
            ->view('pdf.borrowers', [
                'records' => app(GetBorrowersReportDetailsAction::class)->filters($this->getFilters())->execute(),
                'partner' => auth()->user()->partner,
                'filters' => $this->getFilters()
            ])->streamFromLivewire();
    }

    public function excelExport()
    {
        $filename = str((new \ReflectionClass($this))->getShortName())->snake();

        return Excel::download(new BorrowersExport($this->getFilters()), $filename.now()->toDateString().'.xlsx');
    }

    private function getReportData()
    {
        return app(GetBorrowersReportDetailsAction::class)
            ->paginate()
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
}
