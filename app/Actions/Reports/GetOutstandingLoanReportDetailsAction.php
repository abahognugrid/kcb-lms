<?php

namespace App\Actions\Reports;

use App\Enums\LoanAccountType;
use App\Models\Loan;
use App\Models\LoanPenalty;
use Illuminate\Support\Arr;
use App\Models\LoanSchedule;
use Illuminate\Support\Carbon;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Services\Account\AccountSeederService;

class GetOutstandingLoanReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    protected bool $includeWrittenOffLoans = false;
    public ?int $loanProductId = null;
    protected ?int $partnerId;

    public function execute()
    {
        $query = Loan::query()
            ->with('customer')
            ->where('partner_id', $this->partnerId)
            ->whereDate('Credit_Account_Date', '<=', $this->endDate)
            ->whereIn('Credit_Account_Status', $this->getIncludedAccountStatuses())
            ->withSum('schedule', 'total_outstanding')
            ->addSelect([
                'due_date' => function ($query) {
                    $query->selectRaw("COALESCE(loan_schedules.payment_due_date, NULL)")
                        ->from('loan_schedules')
                        ->whereColumn('loan_schedules.loan_id', 'loans.id')
                        ->whereDate('loan_schedules.payment_due_date', '<=', $this->endDate)
                        ->latest('payment_due_date')
                        ->limit(1);
                },
                'principal_outstanding' => function ($query) {
                    $query->selectRaw('loans."Facility_Amount_Granted"::DECIMAL - COALESCE(SUM(journal_entries.credit_amount), 0)')
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
                        ->whereRaw('loan_repayments."Loan_ID" = loans.id')
                        ->whereRaw('loan_repayments."Transaction_Date" <= ?', [$this->endDate]);
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
                WHERE loan_repayments."Loan_ID" = loans.id
                    AND DATE(loan_repayments."Transaction_Date") <= ?)
            ', [LoanRepayment::class, AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG, $this->endDate]);
                },
                'penalty_outstanding' => function ($query) {
                    $query->selectRaw('
                (SELECT COALESCE(SUM("Amount_To_Pay"), 0)
                FROM loan_penalties
                WHERE "Loan_ID" = loans.id
            ) - (
                SELECT COALESCE(SUM(journal_entries.credit_amount), 0)
                FROM loan_repayments
                INNER JOIN journal_entries ON journal_entries.transactable_id = loan_repayments.id
                    AND journal_entries.transactable IN (?, ?) AND journal_entries.partner_id = loan_repayments.partner_id
                INNER JOIN accounts ON accounts.id = journal_entries.account_id
                    AND accounts.slug IN (?)
                WHERE loan_repayments."Loan_ID" = loans.id
                    AND DATE(loan_repayments."Transaction_Date") <= ?)
            ', [LoanRepayment::class, LoanPenalty::class, AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG, $this->endDate]);
                },
                'total_past_due' => LoanSchedule::query()
                    ->selectRaw('sum(total_outstanding)')
                    ->whereColumn('loan_id', 'loans.id')
                    ->whereDate('payment_due_date', '<', $this->endDate),
                'total_pending_due' => LoanSchedule::query()
                    ->selectRaw('sum(total_outstanding)')
                    ->whereColumn('loan_id', 'loans.id')
                    ->whereDate('payment_due_date', '>', $this->endDate),
            ]);

        // Remove any HAVING or problematic WHERE clauses and filter in PHP
        $loans = $query->get()->filter(function ($loan) {
            $principal = $loan->principal_outstanding ?? 0;
            $interest = $loan->interest_outstanding ?? 0;
            $penalty = $loan->penalty_outstanding ?? 0;

            return ($principal + $interest + $penalty) > 0;
        });
        $query->orderBy('Credit_Account_Date', 'desc');


        if ($this->loanProductId) {
            $query->where('loan_product_id', $this->loanProductId);
        }

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

        $this->includeWrittenOffLoans = Arr::get($details, 'includeWrittenOffLoans', false);
        $this->loanProductId = Arr::get($details, 'loanProductId');
        $this->partnerId = Arr::get($details, 'partnerId');

        return $this;
    }

    protected function getIncludedAccountStatuses(): array
    {
        $creditAccountStatuses = [
            LoanAccountType::WithinTerms->value,
            LoanAccountType::BeyondTerms->value,
        ];

        if ($this->includeWrittenOffLoans) {
            $creditAccountStatuses[] = LoanAccountType::WrittenOff->value;
        }

        return $creditAccountStatuses;
    }
}
