<?php

namespace App\Models\Transactables;

use App\Exceptions\InsufficientAccountBalanceException;
use App\Exceptions\JournalEntrySaveFailedException;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTransaction extends Model
{
    public const JOURNAL_ENTRIES_UNBALANCED_ERROR = 'Transaction journal entries did not balance.';
    public const INSUFFICIENT_ACCOUNT_BALANCE_ERROR = "The account '%s' has insufficient funds to cover this transaction.";
    public const JOURNAL_ENTRY_AMOUNTS_NOT_FOUND_ERROR = 'Could not find journal entry amounts for transaction.';

    public const EQUALITY_THRESHOLD = 0.01;

    /**
     * @var JournalEntry[]
     */
    protected array $transactions;

    /**
     * Attempt to save the transaction journal entries.
     *
     * @param int|null $transactionId
     * @return void
     * @throws InsufficientAccountBalanceException
     * @throws JournalEntrySaveFailedException
     */
    public function saveJournalEntries(?int $transactionId = null): void
    {
        $this->makeJournalEntries();

        $this->checkJournalEntriesBalance();

        $this->checkJournalEntriesAccountBalances();

        $this->saveTransactionJournalEntries($transactionId);
    }

    protected abstract function makeJournalEntries(): void;

    /**
     * @throws JournalEntrySaveFailedException
     */
    private function checkJournalEntriesBalance(): void
    {
        $credits_total = 0;
        $debits_total = 0;

        foreach ($this->transactions as $entry) {
            if ($entry->credit_amount > 0) {
                $credits_total = bcadd($credits_total, $entry->credit_amount, 2);
            } elseif ($entry->debit_amount > 0) {
                $debits_total = bcadd($debits_total, $entry->debit_amount, 2);
            }
        }

        $exception_payload = [
            'entries' => $this->transactions,
            'credits_total' => $credits_total,
            'debits_total' => $debits_total,
        ];

        if ($credits_total + $debits_total === 0) {
            throw new JournalEntrySaveFailedException(
                self::JOURNAL_ENTRY_AMOUNTS_NOT_FOUND_ERROR,
                $exception_payload
            );
        }

        if (abs($credits_total - $debits_total) >= self::EQUALITY_THRESHOLD) {
            throw new JournalEntrySaveFailedException(
                self::JOURNAL_ENTRIES_UNBALANCED_ERROR,
                $exception_payload
            );
        }
    }

    /**
     * @throws InsufficientAccountBalanceException
     */
    private function checkJournalEntriesAccountBalances(): void
    {
        $account_effects = [];

        foreach ($this->transactions as $entry) {
            $account = $entry->account;
            if (!isset($account_effects[$account->id])) {
                $account_effects[$account->id] = [
                    'account_name' => $account->name,
                    'current_balance' => $account->current_balance,
                    'is_credit_normal' => $account->isCreditNormal(),
                    'credit_normal_effect' => 0
                ];
            }

            $account_effects[$account->id]['credit_normal_effect'] += ($entry['credit_amount'] - $entry['debit_amount']);
        }

        foreach ($account_effects as $account_effect) {
            $eventual_balance = $account_effect['current_balance'] - $account_effect['credit_normal_effect'];

            if ($account_effect['is_credit_normal']) {
                $eventual_balance = $account_effect['current_balance'] + $account_effect['credit_normal_effect'];
            }

            if ($eventual_balance < 0) {
                $exception_payload = [
                    'entries' => $this->transactions,
                ];

                throw new InsufficientAccountBalanceException(
                    sprintf(self::INSUFFICIENT_ACCOUNT_BALANCE_ERROR, $account_effect['account_name']),
                    $exception_payload
                );
            }
        }
    }

    private function saveTransactionJournalEntries(?int $transactionId = null): void
    {
        $txn_id = rand(11111, 99999) . '-' . now()->unix();
        $transactable_id = $this->id;
        $transactable = get_class($this);

        foreach ($this->transactions as $journal_entry) {
            $journal_entry->transactable_id = $transactable_id;
            $journal_entry->transactable = $transactable;
            $journal_entry->txn_id = $txn_id;
            $journal_entry->transaction_id = $transactionId;
            $journal_entry->credit_amount = $journal_entry->credit_amount ?? 0;
            $journal_entry->debit_amount = $journal_entry->debit_amount ?? 0;

            if (!$journal_entry->save()) {
                throw new JournalEntrySaveFailedException(
                    'Could not save journal entry.',
                    $journal_entry->getAttributes()
                );
            }
        }
    }

    public function journal_entries()
    {
        return $this->morphMany('App\Models\JournalEntry', 'journable', 'transactable', 'transactable_id');
    }
}
