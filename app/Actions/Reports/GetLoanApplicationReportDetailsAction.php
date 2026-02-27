<?php

namespace App\Actions\Reports;

use App\Models\Loan;
use Illuminate\Support\Arr;
use App\Models\JournalEntry;
use Illuminate\Support\Carbon;
use App\Models\LoanApplication;
use App\Models\LoanDisbursement;

class GetLoanApplicationReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    protected ?int $loanProductId = null;
    protected int $partnerId = 0;

    public function execute()
    {
        $query = LoanApplication::query()
            ->with(['customer', 'loan_product'])
            ->has('transaction')
            ->where('partner_id', $this->partnerId)
            ->when($this->loanProductId, function ($query) {
                $query->where('loan_product_id', $this->loanProductId);
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
