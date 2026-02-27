<?php

namespace App\Actions\OtherReports;

use App\Models\CreditLimit;
use Illuminate\Support\Arr;

class GetCreditLimitsReportDetailsAction
{
    protected bool $paginate = false;
    protected array $filters = [];

    public function paginate(): self
    {
        $this->paginate = true;

        return $this;
    }

    public function filters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function execute()
    {
        $query = CreditLimit::query()
            ->where('partner_id', Arr::get($this->filters, 'partnerId'))
            ->when(isset($this->filters['startDate']), function ($query) {
                $query->whereDate('created_at', '>=', $this->filters['startDate']);
            })
            ->when(isset($this->filters['endDate']), function ($query) {
                $query->whereDate('created_at', '<=', $this->filters['endDate']);
            })
            ->orderBy('created_at', 'desc');

        return $this->paginate ? $query->paginate(25) : $query->get();
    }
}
