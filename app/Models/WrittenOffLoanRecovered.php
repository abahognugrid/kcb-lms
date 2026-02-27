<?php

namespace App\Models;

use App\Models\Accounts\Account;
use App\Models\Loan;
use App\Models\Scopes\PartnerScope;
use App\Models\Transactables\BaseTransaction;
use App\Models\Transactables\Contracts\Transactable;
use App\Services\Account\AccountSeederService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OverflowException;

class WrittenOffLoanRecovered extends BaseTransaction implements Transactable
{
    protected $guarded = [];

    protected $table = 'written_off_loans';

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'Loan_ID');
    }

    public function amount()
    {
        return $this->Amount_Written_Off + $this->interest + $this->penalties + $this->fees;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public static function createTransactable(Loan $loan, $amount): WrittenOffLoanRecovered
    {
        [
            $principal_recovered,
            $interest_recovered,
            $penalties,
            $fees
        ] = self::splitWriteOffAmount($loan, $amount);

        return WrittenOffLoanRecovered::create([
            'partner_id' => $loan->partner_id,
            'Loan_ID' => $loan->id,
            'Customer_ID' => $loan->Customer_ID,
            'Written_Off_By' => Auth::user()?->id,
            'Amount_Written_Off' => $principal_recovered,
            'interest' => $interest_recovered,
            'penalties' => $penalties,
            'fees' => $fees,
            'Written_Off_Date' => now()->toDateString(),
            'Is_Recovered' => 1
        ]);
    }

    protected function makeJournalEntries(): void
    {

        $collection_account = Account::query()
            ->where('slug', AccountSeederService::COLLECTION_OVA_SLUG)
            ->where('partner_id', $this->partner_id)
            ->first();

        $recoveries_from_written_off_loans_account = Account::query()
            ->where('slug', AccountSeederService::RECOVERIES_FROM_WRITTEN_OFF_LOANS_SLUG)
            ->where('partner_id', $this->partner_id)
            ->first();

        if (empty($collection_account) || empty($recoveries_from_written_off_loans_account)) {
            Log::error('Missing collection or loan recoveries account');

            throw new Exception('Missing collection or loan recoveries account');
        }

        $this->transactions[] = JournalEntry::make(
            'debit',
            $this->partner_id,
            $this->Customer_ID,
            $collection_account->id,
            $collection_account->name,
            $this->amount(),
            'Non Cash',
        );

        $this->transactions[] = JournalEntry::make(
            'credit',
            $this->partner_id,
            $this->Customer_ID,
            $recoveries_from_written_off_loans_account->id,
            $recoveries_from_written_off_loans_account->name,
            $this->amount(),
            'Non Cash',
        );
    }

    private static function splitWriteOffAmount(Loan $loan, $amount)
    {
        $allocations = [
            'principal' => max(0, ($loan->getOutstandingPrincipalExcludingWriteOffs() - $loan->getOutstandingPrincipal())),
            'interest' => max(0, ($loan->getOutstandingInterestExcludingWriteOffs() - $loan->getOutstandingInterest())),
            'penalties' => max(0, ($loan->getOutstandingPenaltiesExcludingWriteOffs() - $loan->getOutstandingPenalties())),
            'fees' => max(0, ($loan->getOutstandingFeesExcludingWriteOffs() - $loan->getOutstandingFees())),
        ];

        $allocation_results = [
            'principal' => 0,
            'interest' => 0,
            'penalties' => 0,
            'fees' => 0
        ];

        $remaining_amount = $amount;

        foreach ($allocations as $type => $outstanding) {
            if ($remaining_amount <= 0) {
                break;
            }

            $allocated = min($outstanding, $remaining_amount);
            $allocation_results[$type] = $allocated;
            $remaining_amount -= $allocated;
        }

        if (abs(array_sum($allocation_results) - $amount) > 0.01) {
            throw new OverflowException(
                'Amount given is greater than what was written off on this loan.'
            );
        }

        return [
            $allocation_results['principal'],
            $allocation_results['interest'],
            $allocation_results['penalties'],
            $allocation_results['fees']
        ];
    }
}
