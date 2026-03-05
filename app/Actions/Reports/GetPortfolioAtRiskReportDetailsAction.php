<?php

namespace App\Actions\Reports;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanLossProvision;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetPortfolioAtRiskReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected bool $excludeNotDue = false;

    protected int $perPage = 0;

    protected ?int $loanProductId = null;
    protected ?int $partnerId;

    public function execute(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Loan::query()
            ->where('partner_id', $this->partnerId)
            ->whereNotIn('Credit_Account_Status', [
                Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
            ])->whereRelation('schedule', function ($query) {
                $query->where('principal_remaining', '>', 0);
            })
            ->when($this->excludeNotDue, function ($query) {
                $query->whereDoesntHave('schedule', function ($query) {
                    return $query->where('payment_due_date', '>=', $this->endDate);
                });
            })
            ->when($this->loanProductId, function ($query) {
                $query->where('loan_product_id', $this->loanProductId);
            });

        $query->with('customer')
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
            ])
            ->withSum('schedule', 'interest_remaining')
            ->withSum('schedule', 'total_outstanding')
            ->ageingCategories($this->endDate, $this->getProvisions());

        $query->latest();

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
        $this->excludeNotDue = Arr::get($details, 'excludeNotDue', false);

        if (Carbon::parse($this->endDate)->isFuture() || empty($this->endDate)) {
            $this->endDate = now()->toDateString();
        }

        $this->loanProductId = Arr::get($details, 'loanProductId');
        $this->partnerId = Arr::get($details, 'partnerId', 0);

        return $this;
    }

    /**
     * @return LoanLossProvision[]|\Illuminate\Database\Eloquent\Collection
     */
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
