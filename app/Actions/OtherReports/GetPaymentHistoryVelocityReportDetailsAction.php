<?php

namespace App\Actions\OtherReports;

use App\Models\Loan;
use App\Models\LoanSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetPaymentHistoryVelocityReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    protected ?int $loanId = 0;
    protected string $searchTerm = '';

    public function filters(array $filters): self
    {
        $this->startDate = $filters['startDate'] ?? '';
        $this->endDate = $filters['endDate'] ?? '';
        $this->searchTerm = $filters['searchTerm'] ?? '';
        $this->loanId = $filters['loanId'] ?? null;

        return $this;
    }

    public function paginate(int $perPage = 15): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function execute(): \Illuminate\Pagination\LengthAwarePaginator|Collection
    {
        $query = $this->buildQuery();

        if ($this->perPage > 0) {
            return $query->paginate($this->perPage);
        }

        return $query->get();
    }

    private function buildQuery()
    {
        // Get loan schedules with their corresponding repayments
        // We'll calculate payment velocity by comparing due dates with actual payment dates
        $query = DB::table('loan_schedules as ls')
            ->join('loans as l', 'ls.loan_id', '=', 'l.id')
            ->join('customers as c', 'l.Customer_ID', '=', 'c.id')
            ->select([
                'l.id as loan_id',
                'ls.type',
                'ls.principal',
                'ls.interest',
                'ls.installment_number',
                'ls.payment_due_date',
                'ls.total_payment as installment_amount',
                'ls.payment_due_date as schedule_updated_at',
                'ls.updated_at as payment_date',
                'c.Telephone_Number as customer_telephone_number',
                DB::raw('concat(c.First_Name, " ", c.Last_Name) as customer_name'),
                DB::raw('ls.total_payment - ls.total_outstanding payment_amount'),
                DB::raw('DATEDIFF(DATE(ls.updated_at), DATE(ls.payment_due_date)) as days_difference'),
                DB::raw('CASE
                    WHEN DATEDIFF(DATE(ls.updated_at), DATE(ls.payment_due_date)) < 0 THEN "Early"
                    WHEN DATEDIFF(DATE(ls.updated_at), DATE(ls.payment_due_date)) = 0 THEN "On Time"
                    ELSE "Late"
                END as payment_timing'),
                DB::raw('ABS(DATEDIFF(DATE(ls.updated_at), DATE(ls.payment_due_date))) as abs_days_difference')
            ])->where('l.partner_id', auth()->user()->partner_id);

        if (! empty($this->loanId)) {
            $query->where('ls.loan_id', $this->loanId);
        }

        if (! empty($this->searchTerm)) {
            $search = '%' . $this->searchTerm . '%';
            $query->where(function ($query) use ($search) {
                $query->where('l.id', 'LIKE', $search)
                    ->orWhere('c.Telephone_Number', 'LIKE', $search)
                    ->orWhere('c.First_Name', 'LIKE', $search)
                    ->orWhere('c.Last_Name', 'LIKE', $search);
            });
        }

        $query
            ->whereColumn('ls.total_outstanding', '<', 'ls.total_payment')
            //->groupBy('l.id', 'ls.installment_number', 'ls.type', 'ls.principal', 'ls.interest', 'ls.payment_due_date', 'ls.total_payment', 'ls.updated_at', 'c.telephone_number', 'c.first_name', 'c.last_name', 'ls.total_outstanding')
            ->orderBy('l.id')
            ->orderBy('ls.installment_number');

        $query->whereBetween('ls.updated_at', [
            Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
            Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
        ]);

        return $query;
    }

    public function forLoan(?int $loanId): self
    {
        $this->loanId = $loanId;

        return $this;
    }
}
