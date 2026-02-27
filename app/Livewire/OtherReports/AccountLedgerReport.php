<?php

namespace App\Livewire\OtherReports;

use App\Actions\Loans\GetAccountLedgerOpeningBalanceAction;
use App\Actions\OtherReports\GetAccountLedgerReportDetailsAction;
use App\Exports\AccountLedgerExport;
use App\Models\JournalEntry;
use App\Models\LoanProduct;
use App\Services\Account\AccountSeederService;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AccountLedgerReport extends Component
{
    use ExportsData, WithPagination;

    //    public string $accountType = AccountSeederService::COLLECTION_OVA_SLUG;

    public int $loanProductId = 0;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'loanProductId' => ['except' => 0],
    ];

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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedLoanProductId()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.other-reports.account-ledger-report', $this->getViewData());
    }

    public function printReport()
    {
        // Get data to pass to the report
        $viewData = $this->getViewData();
        $viewData['filters'] = $this->getFormattedDateFilters();
        $viewData['partnerName'] = Auth::user()?->partner->Institution_Name;

        return app(PdfGeneratorService::class)
            ->view('pdf.account-ledger', $viewData)
            ->streamFromLivewire();
    }

    public function excelExport()
    {
        return Excel::download(new AccountLedgerExport($this->getFilters()), $this->getExcelFilename());
    }

    protected function getReportData()
    {
        return app(GetAccountLedgerReportDetailsAction::class)
            ->filters($this->getFilters())
            ->paginate()
            ->execute();
    }

    protected function getFilters(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'loanProductId' => $this->loanProductId,
            'search' => $this->search,
        ];
    }

    protected function getViewData(): array
    {
        $records = $this->getReportData();
        $openingBalance = app(GetAccountLedgerOpeningBalanceAction::class)->execute($this->loanProductId, $this->startDate);
        $closingBalance = $openingBalance + data_get($records->last(), 'balance', 0);

        return [
            'records' => $records,
            'summary' => [
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
            ],
            'loanProducts' => LoanProduct::query()
                ->when(Auth::user()->partner_id, function ($query) {
                    return $query->where('partner_id', Auth::user()->partner_id);
                })->orderBy('Name')->pluck('Name', 'id'),
        ];
    }
}
