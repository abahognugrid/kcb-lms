<?php

namespace App\Livewire\OtherReports;

use App\Actions\OtherReports\GetDailyReconciliationReportDetailsAction;
use App\Exports\DailyReconciliationExport;
use App\Models\JournalEntry;
use App\Models\LoanProduct;
use App\Services\Account\AccountSeederService;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class DailyReconciliationReport extends Component
{
    use ExportsData, WithPagination;

    public string $accountType = AccountSeederService::COLLECTION_OVA_SLUG;

    public bool $isDisbursement = false;

    public int $loanProductId = 0;

    public function mount()
    {
        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();
        $this->loanProductId = LoanProduct::query()
            ->select('id')
            ->when(Auth::user()->partner_id, function ($query) {
                return $query->where('partner_id', Auth::user()->partner_id);
            })
            ->first()?->id ?? 0;
    }

    public function render()
    {
        return view('livewire.reports.daily-reconciliation-report', $this->getViewData());
    }

    public function printReport()
    {
        // Get data to pass to the report
        $viewData = $this->getViewData();
        $viewData['filters'] = $this->getFormattedDateFilters();
        $viewData['partnerName'] = Auth::user()?->partner->Institution_Name;

        return app(PdfGeneratorService::class)
            ->view('pdf.daily-reconciliation', $viewData)
            ->streamFromLivewire();
    }

    public function excelExport()
    {
        return Excel::download(new DailyReconciliationExport($this->getFilters()), $this->getExcelFilename());
    }

    private function getReportData()
    {
        return app(GetDailyReconciliationReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function getFilters(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'accountType' => $this->accountType,
            'loanProductId' => $this->loanProductId,
            'isDisbursement' => $this->isDisbursement,
        ];
    }

    protected function getViewData(): array
    {
        $records = $this->getReportData();
        $this->isDisbursement = $this->accountType === AccountSeederService::DISBURSEMENT_OVA_SLUG;

        return [
            'records' => $records,
            'summary' => $this->calculateSummary($records),
            'loanProducts' => LoanProduct::query()
                ->when(Auth::user()->partner_id, function ($query) {
                    return $query->where('partner_id', Auth::user()->partner_id);
                })->orderBy('Name')->pluck('Name', 'id'),
        ];
    }

    protected function calculateSummary($records): array
    {
        $partnerId = Auth::user()->partner_id;

        // Get opening balance for the specific loan product
        $balanceQuery = JournalEntry::query()
            ->join('transactions as t', 'journal_entries.transaction_id', '=', 't.id')
            ->join('loan_applications as la', 't.Loan_Application_ID', '=', 'la.id')
            ->join('accounts as a', 'journal_entries.account_id', '=', 'a.id')
            ->where('a.slug', $this->accountType)
            ->where('la.Loan_Product_ID', $this->loanProductId)
            ->where('journal_entries.partner_id', $partnerId)
            ->whereDate('journal_entries.created_at', '<', $this->startDate)
            ->selectRaw('COALESCE(SUM(journal_entries.debit_amount), 0) as debit_balance, COALESCE(SUM(journal_entries.credit_amount), 0) as credit_balance')
            ->first();

        if ($this->isDisbursement) {
            $openingBalance = $balanceQuery->credit_balance;
        } else {
            $openingBalance = $balanceQuery->debit_balance;
        }

        return [
            'opening_balance' => $openingBalance,
            'closing_balance' => $openingBalance + $records->sum('total_amount'),
        ];
    }
}
