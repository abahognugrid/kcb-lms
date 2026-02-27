<?php

namespace App\Models;

use App\Enums\AccountingType;
use App\Models\Accounts\Account;
use App\Models\Loan;
use App\Models\Scopes\PartnerScope;
use App\Models\Transactables\BaseTransaction;
use App\Models\Transactables\Contracts\Transactable;
use App\Services\Account\AccountSeederService;
use Illuminate\Support\Facades\Auth;

class WrittenOffLoan extends BaseTransaction implements Transactable
{
    protected $guarded = [];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'Loan_ID');
    }

    public function amount()
    {
        return $this->Amount_Written_Off;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public static function createTransactable(Loan $loan, $details): WrittenOffLoan
    {
        $interest = $penalties = $fees = 0;

        if ($loan->partner->Accounting_Type === AccountingType::Accrual->value) {
            $interest = $loan->loan_product->can_write_off_interest ?
                $loan->getOutstandingInterest() : 0;
            $penalties = $loan->loan_product->can_write_off_penalties ?
                $loan->getAccruedPenaltiesBalance() : 0;
            $fees = $loan->loan_product->can_write_off_fees ?
                $loan->getOutstandingFees() : 0;
        }

        return WrittenOffLoan::create([
            'partner_id' => $loan->partner_id,
            'Loan_ID' => $loan->id,
            'Customer_ID' => $loan->Customer_ID,
            'Amount_Written_Off' => $loan->Written_Off_Amount,
            'Written_Off_Date' => $details['write_off_date'],
            'interest' => $interest,
            'penalties' => $penalties,
            'fees' => $fees,
            'Written_Off_By' => Auth::user()?->id,
        ]);
    }

    protected function makeJournalEntries(): void
    {

        $loss_provision_liability_account = $this->loan->loan_product->lossProvisionAccount();

        $this->transactions[] = JournalEntry::make(
            'debit',
            $this->partner_id,
            $this->Customer_ID,
            $loss_provision_liability_account->id,
            $loss_provision_liability_account->name,
            $this->amount(),
            'Non Cash',
        );

        $loan_product_account = $this->loan->loan_product->general_ledger_account;

        $this->transactions[] = JournalEntry::make(
            'credit',
            $this->partner_id,
            $this->Customer_ID,
            $loan_product_account->id,
            $loan_product_account->name,
            $this->amount(),
            'Non Cash',
        );

        $total_fees = 0;

        if ($this->interest > 0) {
            $accrued_interest_balance = $this->loan->getAccruedInterestBalance();

            $interest_receivables_account = Account::where('partner_id', $this->loan->partner_id)
                ->where('slug', AccountSeederService::INTEREST_RECEIVABLES_SLUG)
                ->first();

            $this->transactions[] = JournalEntry::make(
                'credit',
                $this->partner_id,
                $this->Customer_ID,
                $interest_receivables_account->id,
                $interest_receivables_account->name,
                $accrued_interest_balance,
                'Non Cash',
            );

            $interest_income_account = Account::where('partner_id', $this->loan->partner_id)
                ->where('slug', AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG)
                ->first();

            $this->transactions[] = JournalEntry::make(
                'credit',
                $this->partner_id,
                $this->Customer_ID,
                $interest_income_account->id,
                $interest_income_account->name,
                ($this->interest - $accrued_interest_balance),
                'Non Cash',
            );

            $total_fees += $this->interest;
        }

        if ($this->penalties > 0) {
            $penalties_receivables_account = Account::query()
                ->where('partner_id', $this->partner_id)
                ->where('slug', AccountSeederService::PENALTIES_RECEIVABLES_SLUG)
                ->first();

            $this->transactions[] = JournalEntry::make(
                'credit',
                $this->partner_id,
                $this->Customer_ID,
                $penalties_receivables_account->id,
                $penalties_receivables_account->name,
                $this->penalties,
                'Non Cash',
            );

            $total_fees += $this->penalties;
        }

        if ($this->fees > 0) {
            $fees_income_account = Account::query()
                ->where('partner_id', $this->partner_id)
                ->where('slug', AccountSeederService::INCOME_FROM_FINES_SLUG)
                ->first();

            $this->transactions[] = JournalEntry::make(
                'credit',
                $this->partner_id,
                $this->Customer_ID,
                $fees_income_account->id,
                $fees_income_account->name,
                $this->fees,
                'Non Cash',
            );

            $total_fees += $this->fees;
        }

        $provision_for_bad_debts_account = Account::query()
            ->where('partner_id', $this->partner_id)
            ->where('slug', AccountSeederService::PROVISION_FOR_BAD_DEBT_SLUG)
            ->first();

        if ($total_fees > 0) {
            $this->transactions[] = JournalEntry::make(
                'debit',
                $this->partner_id,
                $this->Customer_ID,
                $provision_for_bad_debts_account->id,
                $provision_for_bad_debts_account->name,
                $total_fees,
                'Non Cash',
            );
        }
    }
}
