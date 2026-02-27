<?php

namespace App\Actions\OtherReports;

use App\Actions\Loans\GetAccountLedgerOpeningBalanceAction;
use App\Models\JournalEntry;
use App\Services\Account\AccountSeederService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GetAccountLedgerReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $loanProductId = 0;

    protected string $search = '';

    protected int $perPage = 0;

    public function filters(array $filters): self
    {
        $this->startDate = Arr::get($filters, 'startDate', '');
        $this->endDate = Arr::get($filters, 'endDate', '');
        $this->loanProductId = Arr::get($filters, 'loanProductId', 0);
        $this->search = Arr::get($filters, 'search', '');

        if ($this->paginated()) {
            $this->perPage = Arr::get($filters, 'perPage', 15);
        }

        return $this;
    }

    public function paginate(int $perPage = 50): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    private function paginated(): bool
    {
        return $this->perPage > 0;
    }

    public function execute()
    {
        $partnerId = Auth::user()->partner_id;
        $query = JournalEntry::query()
            ->with([
                'customer:id,First_Name,Last_Name,Telephone_Number',
                'account:id,slug,name',
                'transaction:id,Loan_Application_ID,Loan_ID,Payment_Reference',
                'transaction.loanApplication:id,Loan_Product_ID',
                'transaction.loanApplication.loan:id,Loan_Product_ID,Loan_Application_ID',
            ])
            ->whereHas('transaction.loanApplication', function ($query) {
                $query->where('Loan_Product_ID', $this->loanProductId);
            })
            ->when($this->search, function ($query) {
                $query->whereRelation('transaction', 'payment_reference', 'LIKE', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($customerQuery) {
                        $customerQuery->where(function ($searchQuery) {
                            $searchQuery->whereRaw("CONCAT(First_Name, ' ', Last_Name) LIKE ?", ['%' . $this->search . '%'])
                                ->orWhere('Telephone_Number', 'LIKE', '%' . $this->search . '%');
                        });
                    });
            })
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
            ])
            ->where('partner_id', $partnerId)
            ->orderBy('transaction_id')
            ->orderBy('id', 'desc');

        $openingBalance = app(GetAccountLedgerOpeningBalanceAction::class)->execute($this->loanProductId, $this->startDate);

        $query->afterQuery(function (Collection $journalEntries) use ($openingBalance) {
            return $this->enrichJournalEntryData($journalEntries, $openingBalance);
        });

        if ($this->paginated()) {
            return $query->paginate($this->perPage);
        }

        return $query->get();
    }

    protected function getOpeningBalance(): float|int
    {
        $partnerId = Auth::user()->partner_id;

        // Get opening balance for the specific loan product
        $balanceQuery = JournalEntry::query()
            ->join('transactions as t', 'journal_entries.transaction_id', '=', 't.id')
            ->join('loan_applications as la', 't.Loan_Application_ID', '=', 'la.id')
            ->join('accounts as a', 'journal_entries.account_id', '=', 'a.id')
            ->whereIn('a.slug', [
                AccountSeederService::DISBURSEMENT_OVA_SLUG,
                AccountSeederService::COLLECTION_OVA_SLUG,
            ])
            ->where('la.Loan_Product_ID', $this->loanProductId)
            ->where('journal_entries.partner_id', $partnerId)
            ->whereDate('journal_entries.created_at', '<', $this->startDate)
            ->selectRaw('
                a.slug,
                IF(a.slug = ?, COALESCE(SUM(journal_entries.credit_amount), 0) - COALESCE(SUM(journal_entries.debit_amount), 0), 0) as disbursement_balance,
                IF(a.slug = ?, COALESCE(SUM(journal_entries.debit_amount), 0) - COALESCE(SUM(journal_entries.credit_amount), 0), 0) as collection_balance
            ', [
                AccountSeederService::DISBURSEMENT_OVA_SLUG,
                AccountSeederService::COLLECTION_OVA_SLUG
            ])
            ->groupBy(['a.slug'])
            ->get();

        return $balanceQuery->reduce(function ($carry, $balance) {
            if ($balance->slug === AccountSeederService::DISBURSEMENT_OVA_SLUG) {
                return $carry - $balance->disbursement_balance;
            }

            if ($balance->slug === AccountSeederService::COLLECTION_OVA_SLUG) {
                return $carry + $balance->collection_balance;
            }

            return $carry;
        }, 0);
    }

    /**
     * Enrich journal entry data with calculated running balance
     */
    private function enrichJournalEntryData(Collection $journalEntries, $openingBalance = 0)
    {
        $runningBalance = $openingBalance;

        return $journalEntries->map(function ($journalEntry) use (&$runningBalance) {
            // Calculate balance based on the rules specified
            $balanceChange = $this->calculateBalanceChange($journalEntry);

            $runningBalance += $balanceChange;

            return [
                'id' => $journalEntry->id,
                'loan_id' => $journalEntry->transaction->Loan_ID,
                'payment_reference' => $journalEntry->transaction->Payment_Reference,
                'customer_name' => $journalEntry->customer->name,
                'telephone_number' => $journalEntry->customer->Telephone_Number,
                'account_name' => $journalEntry->account_name,
                'debit_amount' => $journalEntry->debit_amount,
                'credit_amount' => $journalEntry->credit_amount,
                'balance' => $runningBalance,
                'created_at' => $journalEntry->created_at,
                'accounting_type' => $journalEntry->accounting_type,
                'transactable' => $journalEntry->transactable,
                'journal_entry' => $journalEntry,
            ];
        });
    }

    /**
     * Determine if a journal entry is related to a disbursement
     */
    private function isJournalEntryRelatedToDisbursement($journalEntry): bool
    {
        return str_contains($journalEntry->transactable, 'LoanDisbursement');
    }

    /**
     * Calculate the balance change based on the rules specified
     */
    private function calculateBalanceChange(JournalEntry $journalEntry): float
    {
        $amount = $journalEntry->accounting_type === 'debit' ? $journalEntry->debit_amount : $journalEntry->credit_amount;

        $accountSlug = $journalEntry->account->slug;
        if (! in_array($accountSlug, [
            AccountSeederService::DISBURSEMENT_OVA_SLUG,
            AccountSeederService::COLLECTION_OVA_SLUG,
        ])) {
            return 0;
        }

        if ($accountSlug === AccountSeederService::DISBURSEMENT_OVA_SLUG) {
            return -$amount;
        }

        if ($accountSlug === AccountSeederService::COLLECTION_OVA_SLUG) {
            return $amount;
        }

        return 0;
    }
}
