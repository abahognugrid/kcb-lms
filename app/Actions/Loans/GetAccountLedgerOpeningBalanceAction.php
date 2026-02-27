<?php

namespace App\Actions\Loans;

use App\Models\JournalEntry;
use App\Services\Account\AccountSeederService;
use Illuminate\Support\Facades\Auth;

class GetAccountLedgerOpeningBalanceAction
{
    public function execute(int $loanProductId, $startDate): float|int
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
            ->where('la.Loan_Product_ID', $loanProductId)
            ->where('journal_entries.partner_id', $partnerId)
            ->whereDate('journal_entries.created_at', '<', $startDate)
            ->selectRaw('
        a.slug,
        CASE 
            WHEN a.slug = ? THEN COALESCE(SUM(journal_entries.credit_amount), 0) - COALESCE(SUM(journal_entries.debit_amount), 0)
            ELSE 0 
        END as disbursement_balance,
        CASE 
            WHEN a.slug = ? THEN COALESCE(SUM(journal_entries.debit_amount), 0) - COALESCE(SUM(journal_entries.credit_amount), 0)
            ELSE 0 
        END as collection_balance
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
}
