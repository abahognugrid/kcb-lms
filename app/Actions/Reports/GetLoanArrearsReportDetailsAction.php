<?php

namespace App\Actions\Reports;

use App\Models\Loan;
use App\Models\LoanPenalty;
use Illuminate\Support\Arr;
use App\Models\LoanSchedule;
use Illuminate\Support\Carbon;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Services\Account\AccountSeederService;

class GetLoanArrearsReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    protected bool $getCountOnly = false;
    protected bool $suspendedInterest = false;
    protected bool $excludeWrittenOffLoans = false;
    protected ?int $loanProductId = null;
    protected int $partnerId;

    public function execute()
    {
        $query = Loan::query()
            ->with('customer')
            ->where('partner_id', $this->partnerId)
            ->whereNotIn('Credit_Account_Status', $this->getExcludedAccountStatuses())
            ->withSum('schedule', 'principal_remaining')
            ->withSum('schedule', 'interest_remaining')
            ->withSum('schedule', 'total_outstanding')
            ->whereRelation('latestOutstandingPayment', function ($query) {
                $query->where('payment_due_date', '<', $this->endDate);
            })
            ->when($this->suspendedInterest, function ($query) {
                $query->whereRaw('datediff(?, Maturity_Date) > ?', [$this->endDate, 60]);
            })
            ->when($this->loanProductId, function ($query) {
                $query->where('loan_product_id', $this->loanProductId);
            })
            ->addSelect([
                'principal_outstanding' => function ($query) {
                    $query->selectRaw('loans.Facility_Amount_Granted - IFNULL(SUM(journal_entries.credit_amount), 0)')
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
                        ->whereDate('loan_repayments.Transaction_Date', '<=', $this->endDate);
                },
                'interest_outstanding' => function ($query) {
                    $query->selectRaw('
                        (SELECT COALESCE(SUM(interest), 0)
                        FROM loan_schedules
                        WHERE loan_id = loans.id
                    ) - (
                        SELECT COALESCE(SUM(journal_entries.amount), 0)
                        FROM loan_repayments
                        INNER JOIN journal_entries ON journal_entries.transactable_id = loan_repayments.id
                            AND journal_entries.transactable = ? AND journal_entries.partner_id = loan_repayments.partner_id
                        INNER JOIN accounts ON accounts.id = journal_entries.account_id
                            AND accounts.slug IN (?)
                        WHERE loan_repayments.Loan_ID = loans.id
                            AND DATE(loan_repayments.Transaction_Date) <= ?)
                    ', [LoanRepayment::class, AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG, $this->endDate]);
                },
                'penalty_outstanding' => function ($query) {
                    $query->selectRaw('
                        (SELECT COALESCE(SUM(Amount_To_Pay), 0)
                        FROM loan_penalties
                        WHERE Loan_ID = loans.id
                    ) - (
                        SELECT COALESCE(SUM(journal_entries.credit_amount), 0)
                        FROM loan_repayments
                        INNER JOIN journal_entries ON journal_entries.transactable_id = loan_repayments.id
                            AND journal_entries.transactable IN (?, ?) AND journal_entries.partner_id = loan_repayments.partner_id
                        INNER JOIN accounts ON accounts.id = journal_entries.account_id
                            AND accounts.slug IN (?)
                        WHERE loan_repayments.Loan_ID = loans.id
                            AND DATE(loan_repayments.Transaction_Date) <= ?)
                    ', [LoanRepayment::class, LoanPenalty::class, AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG, $this->endDate]);
                },
                'total_principal_arrears' => LoanSchedule::query()
                    ->selectRaw('sum(principal_remaining)')
                    ->whereColumn('loan_id', 'loans.id')
                    ->where('total_outstanding', '>', 0)
                    //                    ->whereDate('payment_due_date', '<', $this->endDate)
                    ->limit(1),
                'total_interest_arrears' => LoanSchedule::query()
                    ->selectRaw('sum(interest_remaining)')
                    ->whereColumn('loan_id', 'loans.id')
                    ->where('total_outstanding', '>', 0)
                    //                    ->whereDate('payment_due_date', '<', $this->endDate)
                    ->limit(1),
                'arrear_days' => LoanSchedule::query()
                    ->selectRaw('datediff(payment_due_date, ?)', [$this->endDate])
                    ->whereColumn('loan_id', 'loans.id')
                    ->where('total_outstanding', '>', 0)
                    //                    ->whereDate('payment_due_date', '<', $this->endDate)
                    ->orderBy('payment_due_date')
                    ->limit(1),
                'penalty_amount' => LoanPenalty::query()
                    ->selectRaw('sum(Amount_To_Pay) - sum(amount)')
                    ->whereColumn('loan_id', 'loans.id')
                    ->whereDate('created_at', '<', Carbon::parse($this->endDate)->endOfDay()->toDateTimeString())
                    ->limit(1),
                'penalty_arrears' => LoanPenalty::query()
                    ->selectRaw('sum(Amount_To_Pay) - sum(amount)')
                    ->whereColumn('loan_id', 'loans.id')
                    ->whereDate('created_at', '<', Carbon::parse($this->endDate)->endOfDay()->toDateTimeString())
                    ->limit(1),
                'due_date' => function ($query) {
                    $query->selectRaw('IFNULL(loan_schedules.payment_due_date, "")')
                        ->from('loan_schedules')
                        ->whereColumn('loan_schedules.loan_id', 'loans.id')
                        ->whereDate('loan_schedules.payment_due_date', '<=', $this->endDate)
                        ->latest('payment_due_date')
                        ->limit(1);
                },
            ]);

        if ($this->getCountOnly) {
            return $query->count();
        }

        $query->latest()
            ->afterQuery(function ($loans) {
                $loans->each(function ($loan) {
                    $loan->total_outstanding_amount = $loan->principal_outstanding + $loan->interest_outstanding + $loan->penalty_outstanding;
                    $loan->total_arrears_amount = $loan->total_principal_arrears + $loan->total_interest_arrears + $loan->penalty_arrears;
                    $loan->arrears_rate = $loan->schedule_sum_principal_remaining > 0 ? round($loan->total_principal_arrears / $loan->schedule_sum_principal_remaining * 100, 2) : 0;
                });
            });

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
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());

        if (Carbon::parse($this->endDate)->isFuture() || empty($this->endDate)) {
            $this->endDate = now()->toDateString();
        }

        $this->suspendedInterest = Arr::get($details, 'suspendedInterest', false);
        $this->excludeWrittenOffLoans = Arr::get($details, 'excludeWrittenOffLoans', false);
        $this->loanProductId = Arr::get($details, 'loanProductId');
        $this->partnerId = Arr::get($details, 'partnerId', 0);

        return $this;
    }

    protected function getExcludedAccountStatuses(): array
    {
        $creditAccountStatuses = [
            Loan::ACCOUNT_STATUS_FULLY_PAID_OFF
        ];

        if ($this->excludeWrittenOffLoans) {
            $creditAccountStatuses[] = Loan::ACCOUNT_STATUS_WRITTEN_OFF;
        }

        return $creditAccountStatuses;
    }
}
