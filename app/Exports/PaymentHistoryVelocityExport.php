<?php

namespace App\Exports;

use App\Actions\OtherReports\GetPaymentHistoryVelocityReportDetailsAction;
use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PaymentHistoryVelocityExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.payment-history-velocity', [
            'records' => app(GetPaymentHistoryVelocityReportDetailsAction::class)
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
