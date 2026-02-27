<?php

namespace App\Actions\Reports;

use App\Models\Customer;
use App\Models\LoanApplication;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetPendingDisbursementReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $perPage = 0;

    protected ?int $loanProductId = null;

    protected int $partnerId;

    public function execute()
    {
        $query = LoanApplication::with([
            'customer',
            'loan_product',
        ])->where('partner_id', $this->partnerId)
            ->where('Credit_Application_Status', 'Approved')
            ->doesntHave('loan');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('Last_Status_Change_Date', [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
            ]);
        }

        if ($this->loanProductId) {
            $query->where('loan_product_id', $this->loanProductId);
        }

        $query->orderBy(Customer::query()->select('First_Name')->whereColumn('customers.id', 'loan_applications.Customer_ID')->limit(1));

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
