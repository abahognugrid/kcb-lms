<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Services\PdfGeneratorService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class LoanSchedule extends Component
{
    use WithPagination;

    public Loan $loan;

    public bool $summarize = false;

    public function render()
    {
        return view('livewire.loan-schedule', [
            'summaries' => $this->getScheduleSummary(),
            'schedules' => $this->getSchedule(),
        ]);
    }

    protected function getSchedule()
    {
        return $this->loan->schedule->groupBy('installment_number');
    }

    protected function getScheduleSummary(): \Illuminate\Support\Collection
    {
        if (! $this->summarize) {
            return collect([]);
        }

        return $this->loan->schedule()
            ->selectRaw('MONTH(payment_due_date) as payment_month, MONTHNAME(payment_due_date) as payment_month_name, YEAR(payment_due_date) as payment_year, type, sum(principal) as principal, sum(interest) as interest, sum(total_outstanding) as total_outstanding')
            ->groupBy(['payment_year', 'payment_month', 'type', 'payment_month_name'])
            ->orderByRaw('payment_year, payment_month, type, payment_month_name')
            ->toBase()
            ->get()
            ->groupBy(function ($item) {
                return $item->payment_year.$item->payment_month;
            });
    }

    public function printSchedule()
    {
        return app(PdfGeneratorService::class)->view('pdf.loan-schedule', [
            'loan' => $this->loan,
            'schedules' => $this->getSchedule(),
            'summaries' => $this->getScheduleSummary(),
            'filters' => ['startDate' => $this->loan->Credit_Account_Date],
            'partnerName' => $this->loan->partner->Institution_Name,
        ])->streamFromLivewire();
    }
}
