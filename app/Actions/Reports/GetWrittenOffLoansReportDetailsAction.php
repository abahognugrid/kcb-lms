<?php

namespace App\Actions\Reports;

use App\Enums\LoanAccountType;
use App\Models\Customer;
use App\Models\Loan;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetWrittenOffLoansReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $perPage = 0;

    protected bool $showRecoveries = false;

    protected ?int $loanProductId = null;

    protected int $partnerId;

    public function execute()
    {
        $query = Loan::query()
            ->with(['customer', 'writtenOffBy'])
            ->where('partner_id', $this->partnerId)
            ->where('Credit_Account_Status', LoanAccountType::WrittenOff);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('Written_Off_Date', [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
            ]);
        }

        if ($this->showRecoveries) {
            $query->whereNotNull('Last_Recovered_At');
        }

        if ($this->loanProductId) {
            $query->where('loan_product_id', $this->loanProductId);
        }

        $query->withSum('schedule', 'principal_remaining')
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

    public function withRecovery(): self
    {
        $this->showRecoveries = true;

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
