<?php

namespace App\Models;

use App\Models\Accounts\Account;
use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'customer_id',
        'transaction_id',
        'account_id',
        'account_name',
        'cash_type', // Cash In, Cash Out
        'amount',
        'current_balance',
        'previous_balance',
        'txn_id',
        'transactable',
        'transactable_id',
        'accounting_type',
        'credit_amount',
        'debit_amount',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    // Transactable
    public function journable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('journable', 'transactable', 'transactable_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function transaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function entryType(): Attribute
    {
        return Attribute::make(
            get: function () {
                return str($this->transactable)->afterLast('\\Loan')->title()->toString();
            }
        );
    }

    /**
     * @param  string  $entry_type  'credit' or 'debit'
     * @param  int  $account_name
     * @param  string  $cash_type  'Cash In', 'Non Cash', or 'Cash Out'
     */
    public static function make(
        string $entry_type,
        int $partner_id,
        ?int $customer_id,
        int $account_id,
        string $account_name,
        string $amount,
        string $cash_type,
    ): self {
        return new self([
            'partner_id' => $partner_id,
            'customer_id' => $customer_id,
            'account_id' => $account_id,
            $entry_type . '_amount' => $amount,
            'amount' => $amount,
            'accounting_type' => $entry_type,
            'account_name' => $account_name,
            'cash_type' => $cash_type,
        ]);
    }
}
