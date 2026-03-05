<?php

namespace App\Actions\Reports;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Models\LoanApplication;

class GetLoanApplicationReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    protected ?int $loanProductId;
    protected ?int $partnerId;

    public function execute()
    {
        $query = LoanApplication::query()
            ->with(['customer', 'loan_product'])
            ->has('transaction')
            ->when($this->partnerId, function ($query) {
                $query->where('partner_id', $this->partnerId);
            })
            ->when($this->loanProductId, function ($query) {
                $query->where('Loan_Product_ID', $this->loanProductId);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('Credit_Application_Date', [
                    Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                    Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
                ]);
            });



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
        $this->partnerId = Arr::get($details, 'partnerId');

        return $this;
    }
}
