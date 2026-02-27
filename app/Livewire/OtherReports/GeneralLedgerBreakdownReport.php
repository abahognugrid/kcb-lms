<?php

namespace App\Livewire\OtherReports;

use App\Actions\Loans\GetJournalBalancesAction;
use App\Actions\OtherReports\GetGeneralLedgerBreakdownDetailsAction;
use App\Exports\GeneralLedgerBreakdownExport;
use App\Models\JournalEntry;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GeneralLedgerBreakdownReport extends Component
{
    use ExportsData, WithPagination;

    public ?int $accountId = null;

    public function mount()
    {
        $this->startDate = now()->subDay()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.reports.general-ledger-breakdown', $this->getViewData());
    }

    public function printReport()
    {
        // Get data to pass to the report
        $viewData = $this->getViewData();
        $viewData['filters'] = $this->getFormattedDateFilters();
        $viewData['partnerName'] = auth()->user()?->partner->Institution_Name;

        return app(PdfGeneratorService::class)
            ->view('pdf.general-ledger-breakdown', $viewData)
            ->streamFromLivewire();
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excelExport(): BinaryFileResponse
    {
        return Excel::download(
            new GeneralLedgerBreakdownExport($this->getFiltersWithAdditionalData()),
            $this->getExcelFilename()
        );
    }

    private function getReportData()
    {
        return app(GetGeneralLedgerBreakdownDetailsAction::class)
            ->paginate()
            ->filters($this->getFiltersWithAdditionalData())
            ->execute();
    }

    protected function getFiltersWithAdditionalData(): array
    {
        $filters = $this->getFilters();
        $filters['accountId'] = $this->accountId;

        return $filters;
    }

    protected function getViewData(): array
    {
        $records = $this->getReportData();
        $accounts = JournalEntry::query()
            ->selectRaw('distinct account_name, account_id')
            ->pluck('account_name', 'account_id');

        if (empty($this->accountId)) {
            $this->accountId = $accounts->keys()->first();
        }

        $summary = [
            'opening_balance' => 0,
            'closing_balance' => 0,
        ];

        if ($this->accountId !== null) {
            $summary = app(GetJournalBalancesAction::class)
                ->execute($records, $this->accountId, $this->startDate);
        }

        return [
            'records' => $records,
            'accounts' => JournalEntry::query()
                ->selectRaw('distinct account_name, account_id')
                ->pluck('account_name', 'account_id'),
            'summary' => $summary,
        ];
    }
}
