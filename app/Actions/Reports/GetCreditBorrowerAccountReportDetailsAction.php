<?php

namespace App\Actions\Reports;

use App\Enums\LoanAccountType;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetCreditBorrowerAccountReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;

    public function execute()
    {
        return Loan::query()->whereIn('Credit_Account_Status', [
            LoanAccountType::WithinTerms->value,
            LoanAccountType::BeyondTerms->value
        ])
            ->orWhere(function (Builder $query) {
                $query->whereIn('Credit_Account_Status', [
                    LoanAccountType::PaidOff->value,
                    LoanAccountType::WrittenOff->value,
                    LoanAccountType::WrittenOffRecovery->value,
                    2,
                    LoanAccountType::Forfeiture->value,
                ])->whereBetween('Last_Status_Change_Date', [$this->startDate, $this->endDate]);
            })
            ->with(['customer', 'loan_product', 'schedule', 'loan_repayments']);
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

        return $this;
    }
}
