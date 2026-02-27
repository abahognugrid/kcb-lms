<?php

namespace App\Actions\Reports;

use App\Models\Customer;
use App\Models\Loan;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetPaidOffLoansReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $perPage = 0;

    protected ?int $loanProductId = null;

    protected int $partnerId;

    public function execute()
    {
        $query = Loan::query()
            ->with('customer')
            ->where('partner_id', $this->partnerId)
            ->whereIn('Credit_Account_Status', [
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
            ]);

        if ($this->startDate && $this->endDate) {
            $query->whereRelation('schedule', function ($query) {
                /**
                 * Each time a repayment is made, the updated_at column is updated.
                 * We are avoiding using the repayments table that may introduce wrong amounts
                 */
                $query->whereBetween('updated_at', [
                    Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                    Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
                ]);
            });
        }

        if ($this->loanProductId) {
            $query->where('loan_product_id', $this->loanProductId);
        }

        $query->withSum('loan_repayments', 'amount')
            ->orderBy(
                Customer::query()
                    ->select('First_Name')
                    ->whereColumn('customers.id', 'loans.Customer_ID')
                    ->limit(1)
            );

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
        $this->partnerId = Arr::get($details, 'partnerId');

        return $this;
    }
}
