<?php

namespace App\Actions\Reports;

use App\Models\LoanLossProvision;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetProvisionsReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $perPage = 0;

    protected bool $showRecoveries = false;

    public ?int $loanProductId = null;

    protected int $partnerId;

    public function execute()
    {
        $latestBatch = LoanLossProvision::where('loan_product_id', $this->loanProductId)
            ->where('partner_id', $this->partnerId)
            ->where('approved_at', '<', Carbon::parse($this->endDate)->addDay())
            ->max('batch_number');

        $query = LoanLossProvision::query()
            ->where('partner_id', $this->partnerId);

        //        if ($this->startDate && $this->endDate) {
        //            $query->whereBetween('journal_entries.created_at', [
        //                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
        //                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
        //            ]);
        //        }
        if ($this->loanProductId) {
            $query->where('loan_product_id', $this->loanProductId);
        }

        $query
            ->where('approved_at', '<', Carbon::parse($this->endDate)->addDay())
            ->where('batch_number', $latestBatch);

        return $query->orderBy('minimum_days')->get();
    }

    public function paginate($perPage = 100): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function filters(array $details): self
    {
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());

        if (Carbon::parse($this->endDate)->isFuture() || empty($this->endDate)) {
            $this->endDate = now()->toDateString();
        }
        //        $this->startDate = Arr::get($details, 'startDate', now()->startOfMonth()->toDateString());
        //        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());
        $this->loanProductId = Arr::get($details, 'loanProductId', null);
        $this->partnerId = Arr::get($details, 'partnerId');

        //        if (Carbon::parse($this->endDate)->isFuture()) {
        //            $this->endDate = now()->toDateString();
        //        }

        return $this;
    }
}
