<?php

namespace App\Actions\OtherReports;

use App\Models\LoanDisbursement;
use App\Models\LoanPenalty;
use App\Models\LoanRepayment;
use App\Models\WrittenOffLoanRecovered;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetIncomeReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    protected ?int $loanProductId = null;

    public function execute(): \Illuminate\Support\Collection
    {
        $carbonEndDate = Carbon::parse($this->endDate)->endOfDay()->toDateTimeString();

        $query = DB::table('accounts as a')
            ->join('journal_entries as je', 'je.account_id', '=', 'a.id');

        if ($this->loanProductId) {
            $query->leftJoin('loan_repayments as lr', function (JoinClause $join) {
                $join
                    ->on('lr.id', '=', 'je.transactable_id')
                    ->whereIn('je.transactable', [LoanRepayment::class, LoanPenalty::class]);
            });

            $query->leftJoin('loan_disbursements as ld', function (JoinClause $join) {
                $join
                    ->on('ld.id', '=', 'je.transactable_id')
                    ->where('je.transactable', LoanDisbursement::class);
            });

            $query->leftJoin('written_off_loans as wol', function (JoinClause $join) {
                $join
                    ->on('wol.id', '=', 'je.transactable_id')
                    ->where('je.transactable', WrittenOffLoanRecovered::class);
            });

            $query->leftJoin('loans as l', function (JoinClause $join) {
                $join->on('l.id', '=', DB::raw('COALESCE(lr.loan_id, ld.loan_id, wol.loan_id)'));
            });

            $query->where('l.loan_product_id', $this->loanProductId);
        }

        $query
            ->selectRaw('a.name, sum(je.amount) as amount')
            ->where('a.type_letter', 'I')
            ->whereBetween('je.created_at', [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                $carbonEndDate
            ])
            ->where('a.partner_id', Auth::user()->partner_id)
            ->groupBy('a.name');

        return $query->get();
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

        $this->loanProductId = Arr::get($details, 'loanProductId');

        return $this;
    }
}
