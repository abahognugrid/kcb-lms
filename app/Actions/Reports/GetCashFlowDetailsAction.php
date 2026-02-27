<?php

namespace App\Actions\Reports;

use App\Models\JournalEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetCashFlowDetailsAction
{
    protected array $filters = [];

    protected int $perPage = 0;

    protected int $partnerId;

    public function filters(array $filters): self
    {
        $this->filters = $filters;
        $this->partnerId = Arr::get($filters, 'partnerId');

        return $this;
    }

    public function paginate($perPage = 100): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function execute()
    {
        $query = JournalEntry::select('cash_type', 'amount', 'created_at')
            ->where('partner_id', $this->partnerId)
            ->whereDate('created_at', '<=', Carbon::parse($this->filters['endDate'])->endOfDay()->toDateTimeString());

        if ($this->perPage > 0) {
            $journalEntries = $query->paginate($this->perPage);
        } else {
            $journalEntries = $query->get();
        }

        return $journalEntries;
    }
}
