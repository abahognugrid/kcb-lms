<?php

namespace App\Actions\Reports;

use App\Models\Loan;
use App\Models\LoanLossProvision;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetOutstandingAmountsAgeingAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected ?int $loanProductId = null;

    public function execute(): object
    {
        $provisions = $this->getProvisions();
        $partnerId = Auth::user()->partner_id;

        if ($provisions->isEmpty()) {
            return (object) [];
        }

        // Build dynamic CASE statements for each ageing category
        $caseStatements = [];
        $parKeys = [];

        foreach ($provisions as $provision) {
            $minDays = $provision->minimum_days;
            $maxDays = $provision->maximum_days;

            if ($maxDays == 0) {
                // "Above" category - e.g., 180 and above
                $key = "par_{$minDays}_above";
                $condition = "('{$this->endDate}'::date - ls.payment_due_date::date) >= {$minDays}";
            } else {
                // Range category - e.g., 1-30, 31-60, etc.
                $key = "par_{$maxDays}";
                if ($minDays == 0) {
                    $condition = "('{$this->endDate}'::date - ls.payment_due_date::date) <= {$maxDays}";
                } else {
                    $condition = "('{$this->endDate}'::date - ls.payment_due_date::date) >= {$minDays} 
                      AND ('{$this->endDate}'::date - ls.payment_due_date::date) <= {$maxDays}";
                }
            }

            $caseStatements[] = "COALESCE(SUM(CASE WHEN {$condition} THEN ls.principal_remaining ELSE 0 END), 0) as {$key}";
            $parKeys[] = $key;
        }

        $selectStatement = implode(',', $caseStatements);

        $result = DB::table('loan_schedules as ls')
            ->join('loans as l', 'l.id', '=', 'ls.loan_id')
            ->where('l.partner_id', $partnerId)
            ->whereNotIn('l.Credit_Account_Status', [
                Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
            ])
            ->where('ls.payment_due_date', '<', $this->endDate)
            ->where('ls.principal_remaining', '>', 0)
            ->selectRaw("
                {$selectStatement}
            ")
            ->first();

        return $result ?: (object) array_fill_keys($parKeys, 0);
    }

    public function filters(array $details): self
    {
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());

        if (Carbon::parse($this->endDate)->isFuture() || empty($this->endDate)) {
            $this->endDate = now()->toDateString();
        }

        return $this;
    }

    public function getProvisions(): mixed
    {
        return LoanLossProvision::query()
            ->select('id', 'minimum_days', 'maximum_days', 'batch_number')
            ->where('batch_number', function ($subquery) {
                $subquery->select(DB::raw('MAX(batch_number)'))
                    ->from('loan_loss_provisions');
            })
            ->orderBy('minimum_days')
            ->get();
    }
}
