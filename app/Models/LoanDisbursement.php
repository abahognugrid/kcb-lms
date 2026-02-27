<?php

namespace App\Models;

use App\Enums\AccountingType;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Customer;
use App\Models\Accounts\Account;
use App\Models\Scopes\PartnerScope;
use App\Models\Transactables\BaseTransaction;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\Account\AccountSeederService;
use App\Models\Transactables\Contracts\Transactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class LoanDisbursement extends BaseTransaction implements Transactable
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "customer_id",
        "partner_id",
        "loan_id",
        "disbursement_date",
        "amount",
        "partner_notified",
        "partner_notified_date"
    ];

    protected function casts(): array
    {
        return [
            'disbursement_date' => 'datetime'
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public static function createDisbursement(Loan $loan): LoanDisbursement
    {
        return LoanDisbursement::create([
            "loan_id" => $loan->id,
            "disbursement_date" => $loan->Credit_Account_Date,
            "amount" => $loan->Credit_Amount,
            "partner_id" => $loan->partner_id,
            "customer_id" => $loan->Customer_ID,
        ]);
    }

    public function journalEntries(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'journable', 'transactable', 'transactable_id')->chaperone();
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }


    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function amount()
    {
        return $this->amount;
    }

    protected function makeJournalEntries(): void
    {
        $disbursementFees = $this->loan->fees->where('Charge_At', 'Disbursement')->where('Status', 'Fully Paid');

        $loan_product = $this->loan->loan_product;
        $total_ledger_fees_amount = 0;

        foreach ($disbursementFees as $disbursementFee) {
            $fees_account = Account::query()
                ->where('id', $disbursementFee->Payable_Account_ID)
                ->where('partner_id', $this->partner_id)
                ->first();

            if (!$fees_account) {
                $fees_account = Account::where("partner_id", $loan_product->partner_id)
                    ->where("slug", AccountSeederService::INCOME_FROM_FINES_SLUG)
                    ->first();
            }

            $total_fees_amount = $disbursementFee->Amount;

            $this->transactions[] = JournalEntry::make(
                'credit',
                $this->partner_id,
                $this->customer_id,
                $fees_account->id,
                $fees_account->name,
                $total_fees_amount,
                'Cash In',
            );

            $total_ledger_fees_amount += $total_fees_amount;
        }

        // Debit Record
        $loan_product = $this->loan->loan_product;
        $loan_product_account = $loan_product->general_ledger_account;
        $loanProductAmount = $this->amount();

        $this->transactions[] = JournalEntry::make(
            'debit',
            $this->partner_id,
            $this->customer_id,
            $loan_product_account->id,
            $loan_product_account->name,
            $loanProductAmount,
            'Cash In',
        );

        $disbursement_account = Account::where('partner_id', $this->partner_id)
            ->where('slug', AccountSeederService::DISBURSEMENT_OVA_SLUG)
            ->first();
        $entryAmount = $this->amount() - $total_ledger_fees_amount;

        $this->transactions[] = JournalEntry::make(
            'credit',
            $this->partner_id,
            $this->customer_id,
            $disbursement_account->id,
            $disbursement_account->name,
            $entryAmount,
            'Cash In',
        );
    }
}
