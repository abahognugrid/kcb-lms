<?php

namespace App\Actions\Reports;

use App\Actions\Loans\GetJournalBalancesAction;
use App\Actions\OtherReports\GetGeneralLedgerBreakdownDetailsAction;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\Partner;
use App\Services\Account\AccountSeederService;
use App\Traits\ExportsData;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetPerformanceMetricsReportDetailsAction
{
    use ExportsData;

    protected int $perPage = 0;

    public function execute()
    {
        $end = Carbon::parse($this->endDate)->toDateString();
        $start = Carbon::parse($this->startDate)->toDateString();

        // Opening balance must be the day before the period starts
        $openingDate = Carbon::parse($this->startDate)->subDay()->toDateString();

        $partnerId = Partner::first()->id;

        /**
         * 1) Loan-only aggregates (no schedule join => no duplication)
         */
        $loanAsAt = DB::table('loans as l')
            ->where('l.partner_id', $partnerId)
            ->selectRaw('
        count(distinct case when l."Credit_Account_Date" between ? and ? then l.id end) as loans_count,
        count(distinct case when l."Credit_Account_Date" between ? and ? then l."Customer_ID" end) as borrowers_count,
        count(distinct case when l."Credit_Account_Date" between ? and ? and l."Credit_Account_Status" = ? then l.id end) as active_loans_count,
        count(distinct case when l."Credit_Account_Date" between ? and ? and l."Credit_Account_Status" = ? then l."Customer_ID" end) as active_borrowers_count,
        count(distinct case when l."Credit_Account_Status" not in (?, ?) and l."Credit_Account_Date" between ? and ? then l.id end) as open_loans_count,
        coalesce(sum(case when l."Credit_Account_Date" between ? and ? then l."Facility_Amount_Granted"::DECIMAL else 0::DECIMAL end),0) as principal_portfolio
    ', [
                $start,
                $end,
                $start,
                $end,
                $start,
                $end,
                Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS,
                $start,
                $end,
                Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS,
                Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
                $start,
                $end,
                $start,
                $end,
            ])
            ->first();
        /**
         * 2) Schedule-driven aggregates (coalesce(sums/aging) - removed incorrect PAR calculations
         */
        $schedAsAt = DB::table('loan_schedules as ls')
            ->join('loans as l', 'l.id', '=', 'ls.loan_id')
            ->where('l.partner_id', $partnerId)
            ->selectRaw('
                coalesce(sum(case when l."Credit_Account_Status" not in (?, ?) and ls.payment_due_date <= ? then ls.total_outstanding else 0 end),0) as active_outstanding_balance,
                coalesce(sum(case when ls.principal_remaining <> ls.principal and ls.payment_due_date between ? and ? then (ls.principal - ls.principal_remaining) else 0 end),0) as principal_paid,
                coalesce(sum(case when l."Credit_Account_Status" = ? and ls.principal_remaining <> ls.principal and ls.payment_due_date between ? and ? then (ls.principal - ls.principal_remaining) else 0 end),0) as active_principal_paid,
                coalesce(sum(case when ls.payment_due_date < ? and ls.principal_remaining > 0 then ls.principal_remaining else 0 end),0) as principal_past_due,
                coalesce(sum(case when l."Credit_Account_Status" = ? and ls.payment_due_date < ? and ls.principal_remaining > 0 then ls.principal_remaining else 0 end),0) as active_principal_past_due
            ', [
                Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
                $end,
                $start,
                $end,
                Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS,
                $start,
                $end,
                $end,
                Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS,
                $end,
            ])
            ->first();

        // Get PAR amounts by ageing categories
        $ageingAction = app(GetOutstandingAmountsAgeingAction::class);
        $parAmounts = $ageingAction->filters(['endDate' => $this->endDate])->execute();

        // Map partner-specific account ids

        $interestAccountIds = DB::table('accounts')
            ->where('partner_id', $partnerId)
            ->whereIn('accounts.slug', [
                AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG,
            ])->pluck('id')->all();
        $feesAccountId = DB::table('accounts')
            ->where('partner_id', $partnerId)
            ->where('slug', AccountSeederService::INCOME_FROM_FINES_SLUG)
            ->value('id');

        $gnugridFeesAccountId = DB::table('accounts')
            ->where('partner_id', $partnerId)
            ->where('slug', AccountSeederService::GNUGRID_FEES_ACCOUNT_SLUG)
            ->value('id');

        $penaltiesAccountId = DB::table('accounts')
            ->where('partner_id', $partnerId)
            ->where('slug', AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG)
            ->value('id');

        // get all principal GL account IDs for this partner
        $principalAccountId = DB::table('accounts')
            ->where('partner_id', $partnerId)
            ->where('accountable_type', LoanProduct::class)
            ->orderByDesc('id')
            ->value('id');

        $payments = DB::table('journal_entries as je')
            ->where('je.partner_id', $partnerId)
            ->whereBetween(DB::raw('date(je.created_at)'), [$start, $end])
            ->selectRaw('
                coalesce(sum(case when je.account_id = ? then abs(je.credit_amount) else 0 end),0) as principal_paid,
                coalesce(sum(case when transactable = ? and je.account_id in (' . implode(',', $interestAccountIds ?: [0]) . ') then abs(je.amount) else 0 end),0) as interest_paid,
                coalesce(sum(case when je.account_id = ? then abs(je.credit_amount) else 0 end),0) as fees_paid,
                coalesce(sum(case when je.account_id = ? then abs(je.credit_amount) else 0 end),0) as gnugrid_fees_paid,
                coalesce(sum(case when je.account_id = ? then abs(je.credit_amount) else 0 end),0) as penalties_paid
            ', [
                $principalAccountId,
                LoanRepayment::class,
                $feesAccountId,
                $gnugridFeesAccountId,
                $penaltiesAccountId,
            ])
            ->first();
        // Loan counts remain as before
        $loanRange = DB::table('loans as l')
            ->where('l.partner_id', $partnerId)
            ->selectRaw(
                'COUNT(DISTINCT CASE
            WHEN l."Credit_Account_Status" = ?
             AND l."Credit_Account_Date" >= ?
             AND l."Credit_Account_Date" < ?::DATE + INTERVAL \'1 day\'
            THEN l.id END) AS paid_loans_count,
         COUNT(DISTINCT CASE
            WHEN l."Credit_Account_Status" = ?
             AND l."Credit_Account_Date" >= ?
             AND l."Credit_Account_Date" < ?::DATE + INTERVAL \'1 day\'
            THEN l.id END) AS active_loans_count',
                [
                    Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
                    $start,
                    $end,  // paid
                    Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS,
                    $start,
                    $end,  // active
                ]
            )
            ->first();

        $range = (object) array_merge((array) $loanRange, (array) $payments);

        $disbursementAccountId = DB::table('accounts')
            ->where('partner_id', $partnerId)
            ->where('slug', AccountSeederService::DISBURSEMENT_OVA_SLUG)
            ->value('id');

        $journalEntries = app(GetGeneralLedgerBreakdownDetailsAction::class)
            ->filters([
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'accountId' => $disbursementAccountId,
            ])
            ->execute();
        $disbursementBalances = app(GetJournalBalancesAction::class)
            ->execute($journalEntries, $disbursementAccountId, $this->startDate);

        // combine loan + schedule metrics
        $asAt = (object) array_merge((array) $loanAsAt, (array) $schedAsAt, $disbursementBalances);

        return (object) [
            'asAt' => $asAt,
            'inRange' => $range,
            /**
             * We will loop through the par amounts so that we shall only show non-zero par classes in the report
             * This way, we shall accommodate custom ageing classes.
             */
            'par' => collect($parAmounts)
                ->mapWithKeys(function ($value, $key) {
                    return [str($key)->replace('_', ' ')->upper()->toString() => $value];
                })->toArray(),
        ];
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

        if (empty($this->endDate) || Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        return $this;
    }
}
