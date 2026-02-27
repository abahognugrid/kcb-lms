<?php

namespace App\Actions\OtherReports;

use App\Models\JournalEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetGeneralLedgerBreakdownDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected ?int $accountId;
    protected int $perPage = 0;
    public function execute(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = JournalEntry::query()
            ->with(['customer', 'account'])
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
            ])
            ->where('partner_id', auth()->user()->partner_id)
            ->when($this->accountId, function ($query, $accountId) {
                $query->where('account_id', $accountId);
            });

        $query->orderBy('created_at');

        //        if ($this->perPage > 0) {
        //            return $query->paginate($this->perPage);
        //        }

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
        $this->accountId = Arr::get($details, 'accountId');

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        if (Carbon::parse($this->startDate)->isAfter($this->endDate)) {
            $this->startDate = $this->endDate;
        }

        return $this;
    }
}
