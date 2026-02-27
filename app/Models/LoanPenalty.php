<?php

namespace App\Models;

use App\Models\Accounts\Account;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Scopes\PartnerScope;
use App\Models\LoanProductPenalties;
use App\Models\Transactables\BaseTransaction;
use App\Models\Transactables\Contracts\Transactable;
use App\Services\Account\AccountSeederService;
use App\Traits\AccountOperations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Loan;

class LoanPenalty extends BaseTransaction implements Transactable
{
    use HasFactory, SoftDeletes;
    use AccountOperations;

    const FULLY_PAID = 'Fully Paid';
    const NOT_PAID = 'Not Paid';
    const PARTIALLY_PAID = 'Partially Paid';

    protected $fillable = [
        "partner_id",
        "Loan_ID",
        "Product_Penalty_ID",
        "Customer_ID",
        "Amount",
        "date",
        "Amount_To_Pay",
        "Status",
    ];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "Customer_ID");
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, "Loan_ID");
    }

    public function loan_product_penalty()
    {
        return $this->belongsTo(LoanProductPenalties::class, "Product_Penalty_ID");
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, "partner_id");
    }

    public function amount(): float
    {
        return $this->Amount_To_Pay;
    }

    protected function makeJournalEntries(): void
    {
        $penaltiesReceivablesAccount = Account::where('partner_id', $this->partner_id)
            ->where('slug', AccountSeederService::PENALTIES_RECEIVABLES_SLUG)
            ->first();

        if (!$penaltiesReceivablesAccount) {
            $penaltiesReceivablesAccount = $this->addPenaltiesReceivableAccount($this->partner_id);
        }

        $this->transactions[] = JournalEntry::make(
            'debit',
            $this->partner_id,
            $this->Customer_ID,
            $penaltiesReceivablesAccount->id,
            $penaltiesReceivablesAccount->name,
            $this->amount(),
            'Cash In',
        );

        $incomeFromPenaltiesAccount = Account::where("partner_id", $this->partner_id)
            ->where("slug", AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG)
            ->first();

        $this->transactions[] = JournalEntry::make(
            'credit',
            $this->partner_id,
            $this->Customer_ID,
            $incomeFromPenaltiesAccount->id,
            $incomeFromPenaltiesAccount->name,
            $this->amount(),
            'Cash In',
        );
    }

    public function journalEntries(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'journable', 'transactable', 'transactable_id');
    }
}
