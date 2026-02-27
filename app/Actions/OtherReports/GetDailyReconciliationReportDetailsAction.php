<?php

namespace App\Actions\OtherReports;

use App\Models\Transaction;
use App\Services\Account\AccountSeederService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetDailyReconciliationReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected string $accountType = AccountSeederService::COLLECTION_OVA_SLUG;

    protected int $loanProductId = 0;

    protected int $perPage = 0;

    public function execute()
    {
        $query = Transaction::query()
            ->with('customer')
            ->withWhereHas('journalEntries', function ($query) {
                $query->whereRelation('account', 'slug', $this->accountType)
                    ->whereBetween('created_at', [
                        Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                        Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
                    ]);
            })
            ->withWhereHas('loanApplication', function ($query) {
                $query->whereRelation('loan_product', 'id', $this->loanProductId);
            })
            ->where('Status', 'Completed')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
            ]);

        $query->latest();

        $query->afterQuery(function (Collection $transactions) {
            return $transactions->map(fn ($transaction) => $this->enrichTransactionData($transaction));
        });

        if ($this->paginate()) {
            return $query->paginate($this->perPage);
        }

        return $query->get();
    }

    /**
     * Enrich transaction data with payment splits and additional details
     */
    private function enrichTransactionData($transaction): array
    {
        // Get the associated loan repayment for this transaction
        $loanRepayment = $transaction->loanRepayment;

        return [
            'transaction' => $transaction,
            'payment_reference' => $transaction->Payment_Reference,
            'narration' => $transaction->Narration,
            'total_amount' => $transaction->Amount,
            'principal_amount' => $loanRepayment?->Principal ?? 0,
            'interest_amount' => $loanRepayment?->Interest ?? 0,
            'fees_amount' => $loanRepayment?->Fee ?? 0,
            'penalty_amount' => $loanRepayment?->Penalty ?? 0,
            'transaction_date' => $transaction->created_at,
            'customer_name' => $transaction->customer->name,
            'loan_id' => $transaction->Loan_ID,
            'transaction_id' => $transaction->id,
            'repayment_id' => $loanRepayment?->id,
        ];
    }

    public function paginate($perPage = 100): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function filters(array $details): self
    {
        $this->startDate = Arr::get($details, 'startDate', now()->toDateString());
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());
        $this->accountType = Arr::get($details, 'accountType', AccountSeederService::COLLECTION_OVA_SLUG);
        $this->loanProductId = Arr::get($details, 'loanProductId', 0);

        if (Carbon::parse($this->endDate)->isFuture() || empty($this->endDate)) {
            $this->endDate = now()->toDateString();
        }

        return $this;
    }
}
