<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    const DISBURSEMENT = 'Disbursement';

    const REPAYMENT = 'Repayment';

    protected $fillable = [
        'partner_id',
        'Type',
        'Status',
        'Telephone_Number',
        'Amount',
        'TXN_ID',
        'Provider_TXN_ID',
        'Payment_Reference',
        'Narration',
        'Loan_ID',
        'Loan_Application_ID',
        'Retry_Count',
        'Approval_Reference',
        'Approval_Date',
        'Rejection_Reference',
        'Rejection_Date',
        'Payment_Service_Provider',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public static function generateID()
    {
        return Str::uuid();
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Telephone_Number', 'Telephone_Number');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'Loan_ID');
    }

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class, 'Loan_Application_ID');
    }

    public function loanRepayment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LoanRepayment::class, 'Transaction_ID');
    }

    public function journalEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function duration(): Attribute
    {
        return Attribute::make(fn() => round(now()->diffInDays($this->created_at, true)));
    }

    public function canBeDisbursed(): bool
    {
        return is_null($this->Loan_ID) && ! is_null($this->Approval_Date) && ! is_null($this->Approval_Reference);
    }
}
