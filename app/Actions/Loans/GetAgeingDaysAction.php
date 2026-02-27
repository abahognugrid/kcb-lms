<?php

namespace App\Actions\Loans;

use App\Models\LoanLossProvision;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class GetAgeingDaysAction
{
    public function execute(): Collection|SupportCollection
    {
        $provisions = LoanLossProvision::query()
            ->select('id', 'minimum_days', 'maximum_days', 'batch_number')
            ->where('batch_number', function ($subquery) {
                $subquery->select(DB::raw('MAX(batch_number)'))->from('loan_loss_provisions');
            })
            ->orderBy('minimum_days')
            ->get();

        if ($provisions->isEmpty()) {
            return collect([
                ['days' => '1 - 30'],
                ['days' => '31 - 60'],
                ['days' => '61 - 90'],
                ['days' => '91 - 180'],
                ['days' => '181 - Above'],
            ])->map(fn ($days) => (object) $days);
        }

        return $provisions;
    }
}
