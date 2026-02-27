<?php

namespace App\Exports;

use App\Actions\Loans\GetAccountLedgerOpeningBalanceAction;
use App\Actions\OtherReports\GetAccountLedgerReportDetailsAction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class AccountLedgerExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    public function view(): \Illuminate\Contracts\View\View
    {

        $records = app(GetAccountLedgerReportDetailsAction::class)
            ->filters($this->filters)
            ->execute();
        $openingBalance = app(GetAccountLedgerOpeningBalanceAction::class)
            ->execute(
                data_get($this->filters, 'loanProductId'),
                data_get($this->filters, 'startDate'),
            );
        $closingBalance = $openingBalance + data_get($records->last(), 'balance', 0);

        return view('excel.account-ledger', [
            'records' => $records,
            'partnerName' => Auth::user()?->partner->Institution_Name,
            'filters' => $this->filters,
            'summary' => [
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
            ],
        ]);
    }

    public function title(): string
    {
        return 'Account Ledger';
    }
}
