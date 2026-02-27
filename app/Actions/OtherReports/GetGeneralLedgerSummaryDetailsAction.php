<?php

namespace App\Actions\OtherReports;

use App\Models\JournalEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetGeneralLedgerSummaryDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    public function execute(): \Illuminate\Support\Collection
    {
        $dateRange = [
            Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
            Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
        ];
        return DB::table('accounts as a')
            ->join('journal_entries as je', 'je.account_id', '=', 'a.id')
            ->selectRaw('a.id, a.name, a.type_letter, sum(je.debit_amount) as total_debit, sum(je.credit_amount) as total_credit')
            ->whereBetween('je.created_at', $dateRange)
            ->where('a.partner_id', auth()->user()->partner_id)
            ->where('je.partner_id', auth()->user()->partner_id)
            ->addSelect([
                'opening_balance' => JournalEntry::query()
                    ->select('previous_balance')
                    ->whereColumn('account_id', 'a.id')
                    ->whereBetween('created_at', $dateRange)
                    ->oldest()
                    ->limit(1),
                'closing_balance' => JournalEntry::query()
                    ->select('current_balance')
                    ->whereColumn('account_id', 'a.id')
                    ->whereBetween('created_at', $dateRange)
                    ->latest()
                    ->limit(1),
            ])
            ->groupBy(['a.id', 'a.name', 'a.type_letter'])
            ->get();
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

        if (Carbon::parse($this->startDate)->isAfter($this->endDate)) {
            $this->startDate = $this->endDate;
        }

        return $this;
    }
}
