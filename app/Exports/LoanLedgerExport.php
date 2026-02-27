<?php

namespace App\Exports;

use App\Actions\OtherReports\GetPaymentHistoryVelocityReportDetailsAction;
use App\Actions\Reports\GetLoanLedgerReportDetailsAction;
use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class LoanLedgerExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.loan-ledger', [
            'records' => app(GetLoanLedgerReportDetailsAction::class)
                ->forLoan($this->filters['loanId'])
                ->filters($this->filters)
                ->execute(),
            'loan' => Loan::query()->find($this->filters['loanId']),
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Payment History Velocity';
    }
}
