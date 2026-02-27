<?php

namespace App\Actions\Reports;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanSchedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetDueLoanReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    protected ?int $loanProductId = null;
    protected int $partnerId = 0;

    public function execute()
    {
        $query = Loan::query()->with(['customer'])
            ->where('partner_id', $this->partnerId)
            ->when($this->loanProductId, function ($query) {
                return $query->where('Loan_Product_ID', $this->loanProductId);
            })
            ->whereNotIn('Credit_Account_Status', [
                Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF
            ])
            ->withSum('schedule', 'principal_remaining')
            ->withSum('schedule', 'total_outstanding')
            ->withSum('schedule', 'total_payment')
            ->withMax('schedule', 'payment_due_date')
            ->whereRelation('schedule', function ($query) {
                $query->whereDate('payment_due_date', '<=', $this->endDate);
            })
            ->addSelect([
                'past_due' => LoanSchedule::query()
                    ->selectRaw('sum(total_outstanding) as total_past_due')
                    ->whereColumn('loan_id', 'loans.id')
                    ->whereDate('payment_due_date', '<', $this->endDate)
                    ->limit(1),
                'pending_due' => LoanSchedule::query()
                    ->selectRaw('sum(total_outstanding) as total_past_due')
                    ->whereColumn('loan_id', 'loans.id')
                    ->whereDate('payment_due_date', '>', $this->endDate)
                    ->limit(1),
                'last_payment_date' => LoanSchedule::query()
                    ->select('updated_at')
                    ->whereColumn('loan_id', 'loans.id')
                    ->orderByDesc('payment_due_date')->limit(1),
            ])
            ->withCasts([
                'last_payment_date' => 'datetime'
            ]);

        $query->latest();

        if ($this->perPage > 0) {
            return $query->paginate($this->perPage);
        }

        return $query->get();
    }

    public function paginate($perPage = 100): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function filters(array $details): self
    {
        $this->startDate = Arr::get($details, 'startDate', now()->startOfMonth()->toDateString());
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        $this->loanProductId = Arr::get($details, 'loanProductId');
        $this->partnerId = Arr::get($details, 'partnerId', 0);

        return $this;
    }
}
