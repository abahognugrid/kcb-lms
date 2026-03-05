<?php

namespace App\Actions\Reports;

use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\WrittenOffLoanRecovered;
use App\Services\Account\AccountSeederService;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetRepaymentReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected ?int $loanProductId = null;
    protected ?int $partnerId;

    protected int $perPage = 0;

    public function execute()
    {
        $partner_id = $this->partnerId;

        $paidInPeriodSubquery = DB::table('loan_repayments as lp1')
            ->selectRaw('lp1."Loan_ID", SUM(je1.amount) AS principal_paid')
            ->join('journal_entries as je1', function ($join) {
                $join->on('je1.transactable_id', '=', 'lp1.id')
                    ->where('je1.transactable', 'App\\Models\\LoanRepayment');
            })
            ->join('accounts as acc', function ($join) {
                $join->on('acc.id', '=', 'je1.account_id')
                    ->where('acc.accountable_type', 'App\\Models\\LoanProduct');
            })
            ->whereDate('lp1.Transaction_Date', '<=', $this->endDate)
            ->groupBy('lp1.Loan_ID');

        $principalArrearsSubQuery = DB::table('loans as l2')
            ->selectRaw('ls2.loan_id, SUM(ls2.principal) AS amount')
            ->join('loan_schedules as ls2', 'ls2.loan_id', '=', 'l2.id')
            ->whereDate('ls2.payment_due_date', '<', $this->endDate)
            ->groupBy('ls2.loan_id');

        $principalDueInPeriodSubquery = DB::table('loans as l3')
            ->selectRaw('ls3.loan_id, SUM(ls3.principal) AS amount')
            ->join('loan_schedules as ls3', 'ls3.loan_id', '=', 'l3.id')
            ->whereDate('ls3.payment_due_date', '<=', $this->endDate)
            ->groupBy('ls3.loan_id');

        $principalDueOnReportingDateSubquery = DB::table('loans as l4')
            ->selectRaw('ls4.loan_id, SUM(ls4.principal) AS amount')
            ->join('loan_schedules as ls4', 'ls4.loan_id', '=', 'l4.id')
            ->whereDate('ls4.payment_due_date', '=', $this->endDate)
            ->groupBy('ls4.loan_id');

        $recoveredInPeriodSubquery = DB::table('written_off_loans as wol')
            ->selectRaw('wol."Loan_ID", SUM(wol."Amount_Written_Off") AS principal_recovered')
            ->join('journal_entries as je2', function ($join) {
                $join->on('je2.transactable_id', '=', 'wol.id')
                    ->where('je2.transactable', WrittenOffLoanRecovered::class);
            })
            ->join('accounts as acc', function ($join) {
                $join->on('acc.id', '=', 'je2.account_id')
                    ->where('acc.slug', AccountSeederService::RECOVERIES_FROM_WRITTEN_OFF_LOANS_SLUG);
            })
            ->whereDate('wol.Written_Off_Date', '<=', $this->endDate)
            ->where('Is_Recovered', 1)
            ->groupBy('wol.Loan_ID');

        $query = Loan::query()
            ->when($this->partnerId, function ($query) {
                $query->where('partner_id', $this->partnerId);
            })->with(['customer', 'loan_repayments', 'loan_schedules', 'write_offs'])
            ->addSelect([
                'due_date' => function ($query) {
                    $query->selectRaw('COALESCE(loan_schedules.payment_due_date, NULL)')
                        ->from('loan_schedules')
                        ->whereColumn('loan_schedules.loan_id', 'loans.id')
                        ->whereDate('loan_schedules.payment_due_date', '>=', $this->startDate)
                        ->whereDate('loan_schedules.payment_due_date', '<=', $this->endDate)
                        ->latest('payment_due_date')
                        ->limit(1);
                },
                'last_payment_date' => LoanRepayment::select('Last_Payment_Date')
                    ->whereColumn('loans.id', 'loan_repayments.Loan_ID')
                    ->latest()
                    ->limit(1),
                'principal_due' => function ($query) {
                    $query->selectRaw('COALESCE(SUM(loan_schedules.principal), 0)')
                        ->from('loan_schedules')
                        ->whereColumn('loan_schedules.loan_id', 'loans.id')
                        ->whereDate('loan_schedules.payment_due_date', '>=', $this->startDate)
                        ->whereDate('loan_schedules.payment_due_date', '<=', $this->endDate);
                },
                'interest_due' => function ($query) {
                    $query->selectRaw('COALESCE(SUM(interest), 0)')
                        ->from('loan_schedules')
                        ->whereColumn('loan_schedules.loan_id', 'loans.id')
                        ->whereDate('loan_schedules.payment_due_date', '>=', $this->startDate)
                        ->whereDate('loan_schedules.payment_due_date', '<=', $this->endDate);
                },
                'fees_due' => LoanSchedule::select(DB::raw('sum(total_outstanding)'))
                    ->whereColumn('loans.id', 'loan_schedules.loan_id')
                    ->where('type', 'like', '%fee%')
                    ->whereDate('payment_due_date', '>=', $this->startDate)
                    ->whereDate('payment_due_date', '<=', $this->endDate)
                    ->limit(1),
                'penalty_due' => function ($query) {
                    $query->selectRaw('COALESCE(SUM("Amount_To_Pay"), 0)')
                        ->from('loan_penalties')
                        ->whereColumn('loan_penalties.Loan_ID', 'loans.id')
                        ->whereDate('loan_penalties.date', '>=', $this->startDate)
                        ->whereDate('loan_penalties.date', '<=', $this->endDate);
                },
                'principal_paid' => function ($query) {
                    $query->selectRaw('COALESCE(SUM(journal_entries.credit_amount), 0) + COALESCE(recovered_in_period.principal_recovered,0)')
                        ->from('loan_repayments')
                        ->join('journal_entries', function ($join) {
                            $join->on('journal_entries.transactable_id', '=', 'loan_repayments.id')
                                ->where('journal_entries.transactable', LoanRepayment::class)
                                ->whereColumn('journal_entries.partner_id', 'loan_repayments.partner_id');
                        })
                        ->join('accounts', function ($join) {
                            $join->on('accounts.id', '=', 'journal_entries.account_id')
                                ->where('accounts.accountable_type', LoanProduct::class);
                        })
                        ->whereColumn('loan_repayments.Loan_ID', 'loans.id')
                        ->whereDate('loan_repayments.Transaction_Date', '>=', $this->startDate)
                        ->whereDate('loan_repayments.Transaction_Date', '<=', $this->endDate);
                },
                'interest_paid' => function ($query) {
                    $query->selectRaw('COALESCE(SUM(journal_entries.amount), 0)')
                        ->from('loan_repayments')
                        ->join('journal_entries', function (JoinClause $join) {
                            $join->on('journal_entries.transactable_id', '=', 'loan_repayments.id')
                                ->where('journal_entries.transactable', LoanRepayment::class)
                                ->whereColumn('journal_entries.partner_id', 'loan_repayments.partner_id');
                        })
                        ->join('accounts', function (JoinClause $join) {
                            $join->on('accounts.id', '=', 'journal_entries.account_id')
                                ->whereColumn('journal_entries.partner_id', 'loan_repayments.partner_id')
                                ->whereIn('accounts.slug', [
                                    AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG,
                                ]);
                        })
                        ->whereColumn('loan_repayments.Loan_ID', 'loans.id')
                        ->whereDate('loan_repayments.Transaction_Date', '>=', $this->startDate)
                        ->whereDate('loan_repayments.Transaction_Date', '<=', $this->endDate);
                },
                'fees_paid' => LoanSchedule::select(DB::raw('sum(total_payment) - sum(total_outstanding)'))
                    ->whereColumn('loans.id', 'loan_schedules.loan_id')
                    ->where('type', 'like', '%fee%')
                    ->limit(1),
                'penalty_paid' => function ($query) {
                    $query->selectRaw('COALESCE(SUM(journal_entries.credit_amount), 0)')
                        ->from('loan_repayments')
                        ->join('journal_entries', function (JoinClause $join) {
                            $join->on('journal_entries.transactable_id', '=', 'loan_repayments.id')
                                ->where('journal_entries.transactable', LoanRepayment::class)
                                ->whereColumn('journal_entries.partner_id', 'loan_repayments.partner_id');
                        })
                        ->join('accounts', function (JoinClause $join) {
                            $join->on('accounts.id', '=', 'journal_entries.account_id')
                                ->whereColumn('journal_entries.partner_id', 'loan_repayments.partner_id')
                                ->whereIn('accounts.slug', [
                                    AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG,
                                ]);
                        })
                        ->whereColumn('loan_repayments.Loan_ID', 'loans.id')
                        ->whereDate('loan_repayments.Transaction_Date', '>=', $this->startDate)
                        ->whereDate('loan_repayments.Transaction_Date', '<=', $this->endDate);
                },
                DB::raw("
    CASE 
        WHEN principal_arrears.amount + COALESCE(principal_due_on_reporting_date.amount, 0) = 0 THEN
            0
        ELSE
            LEAST(
                (COALESCE(paid_in_period.principal_paid,0) + COALESCE(recovered_in_period.principal_recovered,0)),
                principal_due_in_period.amount
            ) /
            (principal_arrears.amount + COALESCE(principal_due_on_reporting_date.amount, 0)) * 100
    END AS repayment_rate
")

            ])
            ->leftJoinSub($paidInPeriodSubquery, 'paid_in_period', function (JoinClause $join) {
                $join->on('paid_in_period.Loan_ID', '=', 'loans.id');
            })
            ->leftJoinSub($recoveredInPeriodSubquery, 'recovered_in_period', function (JoinClause $join) {
                $join->on('recovered_in_period.Loan_ID', '=', 'loans.id');
            })
            ->leftJoinSub($principalArrearsSubQuery, 'principal_arrears', function (JoinClause $join) {
                $join->on('principal_arrears.loan_id', '=', 'loans.id');
            })
            ->leftJoinSub($principalDueInPeriodSubquery, 'principal_due_in_period', function (JoinClause $join) {
                $join->on('principal_due_in_period.loan_id', '=', 'loans.id');
            })
            ->leftJoinSub($principalDueOnReportingDateSubquery, 'principal_due_on_reporting_date', function (JoinClause $join) {
                $join->on('principal_due_on_reporting_date.loan_id', '=', 'loans.id');
            })->orderByDesc('loans.id');

        if ($this->loanProductId) {
            $query->where('Loan_Product_ID', $this->loanProductId);
        }

        if ($this->startDate && $this->endDate) {
            $query->whereRelation('loan_repayments', function ($query) {
                $query->whereBetween('Transaction_Date', [
                    Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                    Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
                ]);
            });

            $query->orWhereRelation('write_offs', function ($query) {
                $query->whereBetween('Written_Off_Date', [
                    Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                    Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
                ]);
                $query->where('Is_Recovered', 1);
            });
        }

        $query->afterQuery(function ($loans) {
            $loans->each(function (Loan $loan) {
                $loan->total_paid = $loan->principal_paid + $loan->interest_paid + $loan->penalty_paid + $loan->fees_paid;
            });
        });

        if ($this->perPage > 0) {
            return $query->paginate($this->perPage);
        }

        return $query->get();
    }

    public function paginate(int $perPage = 100): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function filters(array $details): self
    {
        $this->startDate = Arr::get($details, 'startDate', now()->startOfMonth()->toDateString());
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());
        $this->partnerId = Arr::get($details, 'partnerId');
        $this->loanProductId = Arr::get($details, 'loanProductId');

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        if (Carbon::parse($this->startDate)->isAfter($this->endDate)) {
            $this->startDate = $this->endDate;
        }

        return $this;
    }
}
